<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemSettingController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = SystemSetting::class;
        $this->modelName = 'Paramètre système';
        $this->viewPath = 'system-settings';
    }

    /**
     * Afficher les paramètres système
     */
    public function index(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Organiser les paramètres par catégorie
        $settingsByCategory = SystemSetting::all()->groupBy('category');

        // Définir l'ordre des catégories
        $categories = [
            'general' => 'Paramètres généraux',
            'company' => 'Informations de l\'entreprise',
            'inventory' => 'Gestion des stocks',
            'sales' => 'Paramètres de vente',
            'purchase' => 'Paramètres d\'achat',
            'notifications' => 'Notifications',
            'security' => 'Sécurité',
            'system' => 'Système',
        ];

        $organizedSettings = collect();
        foreach ($categories as $key => $label) {
            if ($settingsByCategory->has($key)) {
                $organizedSettings->put($key, [
                    'label' => $label,
                    'settings' => $settingsByCategory->get($key)
                ]);
            }
        }

        return view('system-settings.index', compact('organizedSettings'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            DB::beginTransaction();

            $updatedSettings = [];

            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $settingKey = str_replace('setting_', '', $key);
                    
                    $setting = SystemSetting::where('key', $settingKey)->first();
                    
                    if ($setting) {
                        // Valider la valeur selon le type
                        $validatedValue = $this->validateSettingValue($setting, $value);
                        
                        $oldValue = $setting->value;
                        $setting->update([
                            'value' => $validatedValue,
                            'updated_by' => auth()->id(),
                        ]);

                        $updatedSettings[] = [
                            'key' => $settingKey,
                            'old_value' => $oldValue,
                            'new_value' => $validatedValue
                        ];
                    }
                }
            }

            DB::commit();

            // Vider le cache des paramètres
            Cache::forget('system_settings');

            // Logger les modifications
            $this->logActivity('update', new SystemSetting(), [], $updatedSettings, 
                'Mise à jour des paramètres système');

            if ($request->expectsJson()) {
                return $this->successResponse($updatedSettings, 'Paramètres mis à jour avec succès');
            }

            return $this->backWithSuccess('Paramètres mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'mise à jour des paramètres');
        }
    }

    /**
     * Réinitialiser un paramètre à sa valeur par défaut
     */
    public function reset(Request $request, SystemSetting $systemSetting)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        try {
            DB::beginTransaction();

            $oldValue = $systemSetting->value;
            $systemSetting->update([
                'value' => $systemSetting->default_value,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('system_settings');

            $this->logActivity('reset', $systemSetting, 
                ['value' => $oldValue], 
                ['value' => $systemSetting->default_value], 
                "Réinitialisation du paramètre {$systemSetting->key}");

            if ($request->expectsJson()) {
                return $this->successResponse($systemSetting, 'Paramètre réinitialisé');
            }

            return $this->backWithSuccess('Paramètre réinitialisé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'réinitialisation du paramètre');
        }
    }

    /**
     * Créer un nouveau paramètre
     */
    public function store(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $validated = $request->validate([
            'key' => 'required|string|max:100|unique:system_settings,key',
            'value' => 'required|string',
            'type' => 'required|in:string,integer,boolean,float,json',
            'category' => 'required|in:general,company,inventory,sales,purchase,notifications,security,system',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Valider la valeur selon le type
            $validatedValue = $this->validateValueByType($validated['value'], $validated['type']);

            $setting = SystemSetting::create([
                'key' => $validated['key'],
                'value' => $validatedValue,
                'default_value' => $validatedValue,
                'type' => $validated['type'],
                'category' => $validated['category'],
                'description' => $validated['description'],
                'is_public' => $validated['is_public'] ?? false,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('system_settings');

            $this->logActivity('create', $setting, [], $setting->toArray(), 
                "Création du paramètre {$setting->key}");

            if ($request->expectsJson()) {
                return $this->successResponse($setting, 'Paramètre créé avec succès');
            }

            return $this->backWithSuccess('Paramètre créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'création du paramètre');
        }
    }

    /**
     * Supprimer un paramètre personnalisé
     */
    public function destroy(SystemSetting $systemSetting)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        // Empêcher la suppression des paramètres système critiques
        if ($systemSetting->is_system) {
            return $this->errorResponse('Les paramètres système ne peuvent pas être supprimés');
        }

        try {
            DB::beginTransaction();

            $settingKey = $systemSetting->key;
            $systemSetting->delete();

            DB::commit();

            // Vider le cache
            Cache::forget('system_settings');

            $this->logActivity('delete', $systemSetting, $systemSetting->toArray(), [], 
                "Suppression du paramètre {$settingKey}");

            if (request()->expectsJson()) {
                return $this->successResponse(null, 'Paramètre supprimé avec succès');
            }

            return $this->backWithSuccess('Paramètre supprimé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'suppression du paramètre');
        }
    }

    /**
     * Export des paramètres
     */
    public function export(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $settings = SystemSetting::all();

        $exportData = $settings->map(function($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'category' => $setting->category,
                'description' => $setting->description,
                'is_public' => $setting->is_public ? 'Oui' : 'Non',
                'is_system' => $setting->is_system ? 'Oui' : 'Non',
                'created_at' => $setting->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $setting->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        $filename = 'parametres_systeme_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'Clé',
                'Valeur', 
                'Type',
                'Catégorie',
                'Description',
                'Public',
                'Système',
                'Créé le',
                'Modifié le'
            ]);

            // Données
            foreach ($exportData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        $this->logActivity('export', new SystemSetting(), [], [], 'Export des paramètres système');

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import des paramètres
     */
    public function import(Request $request)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Retirer l'en-tête
            $header = array_shift($data);
            
            $imported = 0;
            $updated = 0;

            foreach ($data as $row) {
                if (count($row) >= 4) {
                    $key = $row[0];
                    $value = $row[1];
                    $type = $row[2];
                    $category = $row[3];
                    $description = $row[4] ?? null;

                    // Valider la valeur
                    $validatedValue = $this->validateValueByType($value, $type);

                    $setting = SystemSetting::where('key', $key)->first();

                    if ($setting) {
                        // Ne pas écraser les paramètres système
                        if (!$setting->is_system) {
                            $setting->update([
                                'value' => $validatedValue,
                                'updated_by' => auth()->id(),
                            ]);
                            $updated++;
                        }
                    } else {
                        SystemSetting::create([
                            'key' => $key,
                            'value' => $validatedValue,
                            'default_value' => $validatedValue,
                            'type' => $type,
                            'category' => $category,
                            'description' => $description,
                            'is_public' => false,
                            'is_system' => false,
                            'created_by' => auth()->id(),
                        ]);
                        $imported++;
                    }
                }
            }

            DB::commit();

            // Vider le cache
            Cache::forget('system_settings');

            $this->logActivity('import', new SystemSetting(), [], [], 
                "Import de {$imported} nouveaux paramètres et mise à jour de {$updated} paramètres existants");

            if ($request->expectsJson()) {
                return $this->successResponse(
                    ['imported' => $imported, 'updated' => $updated],
                    "Import terminé : {$imported} créés, {$updated} mis à jour"
                );
            }

            return $this->backWithSuccess("Import terminé : {$imported} créés, {$updated} mis à jour");

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'import des paramètres');
        }
    }

    /**
     * Obtenir la valeur d'un paramètre (API)
     */
    public function getValue(Request $request, $key)
    {
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return $this->errorResponse('Paramètre non trouvé', 404);
        }

        // Vérifier si l'utilisateur peut accéder à ce paramètre
        if (!$setting->is_public && !$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse([
            'key' => $setting->key,
            'value' => $setting->getCastedValue(),
            'type' => $setting->type,
        ]);
    }

    /**
     * Définir la valeur d'un paramètre (API)
     */
    public function setValue(Request $request, $key)
    {
        if (!$this->checkRoles(['administrateur'])) {
            return $this->unauthorizedResponse();
        }

        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return $this->errorResponse('Paramètre non trouvé', 404);
        }

        $validated = $request->validate([
            'value' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $validatedValue = $this->validateSettingValue($setting, $validated['value']);
            
            $oldValue = $setting->value;
            $setting->update([
                'value' => $validatedValue,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('system_settings');

            $this->logActivity('update', $setting, 
                ['value' => $oldValue], 
                ['value' => $validatedValue], 
                "Mise à jour du paramètre {$setting->key} via API");

            return $this->successResponse([
                'key' => $setting->key,
                'value' => $setting->getCastedValue(),
            ], 'Paramètre mis à jour');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleDatabaseError($e, 'mise à jour du paramètre');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function validateSettingValue(SystemSetting $setting, $value)
    {
        return $this->validateValueByType($value, $setting->type);
    }

    private function validateValueByType($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return in_array(strtolower($value), ['true', '1', 'yes', 'on']) ? 'true' : 'false';
            
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    throw new \InvalidArgumentException('La valeur doit être un entier');
                }
                return (string)(int)$value;
            
            case 'float':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException('La valeur doit être un nombre');
                }
                return (string)(float)$value;
            
            case 'json':
                $decoded = json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('La valeur doit être un JSON valide');
                }
                return $value;
            
            case 'string':
            default:
                return (string)$value;
        }
    }
}