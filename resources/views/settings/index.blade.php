<?php
// 🎯 VUES PARAMÈTRES SYSTÈME & CONFIGURATION AVANCÉE

// ===================================
// 1. VUE PARAMÈTRES SYSTÈME PRINCIPAL
// ===================================
// File: resources/views/settings/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Paramètres Système')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="settingsManager()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Paramètres Système</h1>
                    <p class="text-sm text-gray-600 mt-1">Configuration générale de StockGuardian</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="exportSettings()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Exporter config</span>
                    </button>
                    <button @click="showBackupModal = true" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Sauvegarde</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Navigation onglets -->
        <div class="mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'general'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Général
                </button>
                <button @click="activeTab = 'company'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'company', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'company'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Entreprise
                </button>
                <button @click="activeTab = 'stock'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'stock', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'stock'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Stock & Alertes
                </button>
                <button @click="activeTab = 'sales'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'sales', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'sales'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Ventes & POS
                </button>
                <button @click="activeTab = 'notifications'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'notifications', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'notifications'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Notifications
                </button>
                <button @click="activeTab = 'advanced'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'advanced', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'advanced'}"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Avancé
                </button>
            </nav>
        </div>

        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Onglet Général -->
            <div x-show="activeTab === 'general'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuration générale</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'application</label>
                            <input type="text" 
                                   id="app_name" 
                                   name="app_name" 
                                   value="{{ old('app_name', $settings['app_name'] ?? 'StockGuardian') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="app_version" class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                            <input type="text" 
                                   id="app_version" 
                                   name="app_version" 
                                   value="{{ old('app_version', $settings['app_version'] ?? '1.0.0') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Fuseau horaire</label>
                            <select id="timezone" 
                                    name="timezone" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Africa/Abidjan" {{ ($settings['timezone'] ?? 'Africa/Abidjan') == 'Africa/Abidjan' ? 'selected' : '' }}>Afrique/Abidjan</option>
                                <option value="Europe/Paris" {{ ($settings['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                            <select id="currency" 
                                    name="currency" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="XOF" {{ ($settings['currency'] ?? 'XOF') == 'XOF' ? 'selected' : '' }}>Franc CFA (XOF)</option>
                                <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>Dollar US (USD)</option>
                            </select>
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Langue</label>
                            <select id="language" 
                                    name="language" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="fr" {{ ($settings['language'] ?? 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ ($settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Format de date</label>
                            <select id="date_format" 
                                    name="date_format" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Entreprise -->
            <div x-show="activeTab === 'company'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de l'entreprise</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise</label>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            <input type="file" 
                                   id="company_logo" 
                                   name="company_logo" 
                                   accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @if(isset($settings['company_logo']) && $settings['company_logo'])
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $settings['company_logo']) }}" 
                                     alt="Logo actuel" 
                                     class="h-16 w-auto rounded border border-gray-200">
                            </div>
                            @endif
                        </div>

                        <div>
                            <label for="company_registration" class="block text-sm font-medium text-gray-700 mb-2">N° d'enregistrement</label>
                            <input type="text" 
                                   id="company_registration" 
                                   name="company_registration" 
                                   value="{{ old('company_registration', $settings['company_registration'] ?? '') }}"
                                   placeholder="RCCM, NIU..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="company_tax_number" class="block text-sm font-medium text-gray-700 mb-2">N° fiscal/TVA</label>
                            <input type="text" 
                                   id="company_tax_number" 
                                   name="company_tax_number" 
                                   value="{{ old('company_tax_number', $settings['company_tax_number'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" 
                                   id="company_phone" 
                                   name="company_phone" 
                                   value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   id="company_email" 
                                   name="company_email" 
                                   value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="md:col-span-2">
                            <label for="company_address" class="block text-sm font-medium text-gray-700 mb-2">Adresse complète</label>
                            <textarea id="company_address" 
                                      name="company_address" 
                                      rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Stock & Alertes -->
            <div x-show="activeTab === 'stock'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gestion des stocks</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_stock_alert_threshold" class="block text-sm font-medium text-gray-700 mb-2">Seuil d'alerte par défaut</label>
                            <input type="number" 
                                   id="default_stock_alert_threshold" 
                                   name="default_stock_alert_threshold" 
                                   value="{{ old('default_stock_alert_threshold', $settings['default_stock_alert_threshold'] ?? 10) }}"
                                   min="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="expiry_alert_days" class="block text-sm font-medium text-gray-700 mb-2">Alerte expiration (jours)</label>
                            <input type="number" 
                                   id="expiry_alert_days" 
                                   name="expiry_alert_days" 
                                   value="{{ old('expiry_alert_days', $settings['expiry_alert_days'] ?? 30) }}"
                                   min="1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="auto_generate_references" class="block text-sm font-medium text-gray-700 mb-2">Génération automatique des références</label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="auto_generate_references" 
                                       value="1"
                                       {{ old('auto_generate_references', $settings['auto_generate_references'] ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Activer la génération automatique</span>
                            </label>
                        </div>

                        <div>
                            <label for="stock_valuation_method" class="block text-sm font-medium text-gray-700 mb-2">Méthode de valorisation</label>
                            <select id="stock_valuation_method" 
                                    name="stock_valuation_method" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="fifo" {{ ($settings['stock_valuation_method'] ?? 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO (Premier entré, premier sorti)</option>
                                <option value="lifo" {{ ($settings['stock_valuation_method'] ?? '') == 'lifo' ? 'selected' : '' }}>LIFO (Dernier entré, premier sorti)</option>
                                <option value="average" {{ ($settings['stock_valuation_method'] ?? '') == 'average' ? 'selected' : '' }}>Coût moyen pondéré</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Types d'alertes à activer</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="alerts[]" 
                                           value="low_stock"
                                           {{ in_array('low_stock', old('alerts', $settings['alerts'] ?? ['low_stock', 'expiry', 'out_of_stock'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Stock faible</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="alerts[]" 
                                           value="expiry"
                                           {{ in_array('expiry', old('alerts', $settings['alerts'] ?? ['low_stock', 'expiry', 'out_of_stock'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Expiration proche</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="alerts[]" 
                                           value="out_of_stock"
                                           {{ in_array('out_of_stock', old('alerts', $settings['alerts'] ?? ['low_stock', 'expiry', 'out_of_stock'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Rupture de stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="alerts[]" 
                                           value="negative_stock"
                                           {{ in_array('negative_stock', old('alerts', $settings['alerts'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Stock négatif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Ventes & POS -->
            <div x-show="activeTab === 'sales'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuration ventes et POS</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Taux de TVA par défaut (%)</label>
                            <input type="number" 
                                   id="default_tax_rate" 
                                   name="default_tax_rate" 
                                   value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? 18) }}"
                                   min="0" 
                                   max="100" 
                                   step="0.01"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 mb-2">Préfixe factures</label>
                            <input type="text" 
                                   id="invoice_prefix" 
                                   name="invoice_prefix" 
                                   value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="receipt_printer" class="block text-sm font-medium text-gray-700 mb-2">Imprimante tickets</label>
                            <select id="receipt_printer" 
                                    name="receipt_printer" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Aucune</option>
                                <option value="thermal_58mm" {{ ($settings['receipt_printer'] ?? '') == 'thermal_58mm' ? 'selected' : '' }}>Thermique 58mm</option>
                                <option value="thermal_80mm" {{ ($settings['receipt_printer'] ?? '') == 'thermal_80mm' ? 'selected' : '' }}>Thermique 80mm</option>
                                <option value="laser_a4" {{ ($settings['receipt_printer'] ?? '') == 'laser_a4' ? 'selected' : '' }}>Laser A4</option>
                            </select>
                        </div>

                        <div>
                            <label for="auto_print_receipts" class="block text-sm font-medium text-gray-700 mb-2">Impression automatique</label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="auto_print_receipts" 
                                       value="1"
                                       {{ old('auto_print_receipts', $settings['auto_print_receipts'] ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Imprimer automatiquement les tickets</span>
                            </label>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Modes de paiement acceptés</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="cash"
                                           {{ in_array('cash', old('payment_methods', $settings['payment_methods'] ?? ['cash', 'card', 'mobile'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Espèces</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="card"
                                           {{ in_array('card', old('payment_methods', $settings['payment_methods'] ?? ['cash', 'card', 'mobile'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Carte bancaire</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="mobile"
                                           {{ in_array('mobile', old('payment_methods', $settings['payment_methods'] ?? ['cash', 'card', 'mobile'])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Mobile Money</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="check"
                                           {{ in_array('check', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Chèque</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="credit"
                                           {{ in_array('credit', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Crédit client</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="payment_methods[]" 
                                           value="bank_transfer"
                                           {{ in_array('bank_transfer', old('payment_methods', $settings['payment_methods'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Virement</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Notifications -->
            <div x-show="activeTab === 'notifications'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuration des notifications</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Email</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="email_notifications[]" 
                                           value="stock_alerts"
                                           {{ in_array('stock_alerts', old('email_notifications', $settings['email_notifications'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Alertes de stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="email_notifications[]" 
                                           value="low_stock"
                                           {{ in_array('low_stock', old('email_notifications', $settings['email_notifications'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Stock faible</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="email_notifications[]" 
                                           value="expiry_alerts"
                                           {{ in_array('expiry_alerts', old('email_notifications', $settings['email_notifications'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Alertes d'expiration</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="email_notifications[]" 
                                           value="daily_reports"
                                           {{ in_array('daily_reports', old('email_notifications', $settings['email_notifications'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Rapports quotidiens</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="notification_email" class="block text-sm font-medium text-gray-700 mb-2">Email de notification</label>
                            <input type="email" 
                                   id="notification_email" 
                                   name="notification_email" 
                                   value="{{ old('notification_email', $settings['notification_email'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Avancé -->
            <div x-show="activeTab === 'advanced'" x-transition class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Paramètres avancés</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="backup_frequency" class="block text-sm font-medium text-gray-700 mb-2">Fréquence de sauvegarde</label>
                            <select id="backup_frequency" 
                                    name="backup_frequency" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="daily" {{ ($settings['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Quotidienne</option>
                                <option value="weekly" {{ ($settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                                <option value="manual" {{ ($settings['backup_frequency'] ?? '') == 'manual' ? 'selected' : '' }}>Manuelle uniquement</option>
                            </select>
                        </div>

                        <div>
                            <label for="log_level" class="block text-sm font-medium text-gray-700 mb-2">Niveau de log</label>
                            <select id="log_level" 
                                    name="log_level" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="error" {{ ($settings['log_level'] ?? 'info') == 'error' ? 'selected' : '' }}>Erreurs uniquement</option>
                                <option value="warning" {{ ($settings['log_level'] ?? 'info') == 'warning' ? 'selected' : '' }}>Erreurs et avertissements</option>
                                <option value="info" {{ ($settings['log_level'] ?? 'info') == 'info' ? 'selected' : '' }}>Informations</option>
                                <option value="debug" {{ ($settings['log_level'] ?? 'info') == 'debug' ? 'selected' : '' }}>Debug (tout)</option>
                            </select>
                        </div>

                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">Timeout session (minutes)</label>
                            <input type="number" 
                                   id="session_timeout" 
                                   name="session_timeout" 
                                   value="{{ old('session_timeout', $settings['session_timeout'] ?? 120) }}"
                                   min="5" 
                                   max="1440"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="max_file_upload_size" class="block text-sm font-medium text-gray-700 mb-2">Taille max upload (MB)</label>
                            <input type="number" 
                                   id="max_file_upload_size" 
                                   name="max_file_upload_size" 
                                   value="{{ old('max_file_upload_size', $settings['max_file_upload_size'] ?? 10) }}"
                                   min="1" 
                                   max="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Options de sécurité</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="security_options[]" 
                                           value="force_https"
                                           {{ in_array('force_https', old('security_options', $settings['security_options'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Forcer HTTPS</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="security_options[]" 
                                           value="two_factor_auth"
                                           {{ in_array('two_factor_auth', old('security_options', $settings['security_options'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Authentification à deux facteurs</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="security_options[]" 
                                           value="ip_whitelist"
                                           {{ in_array('ip_whitelist', old('security_options', $settings['security_options'] ?? [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Liste blanche IP</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" 
                        @click="resetToDefaults()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Valeurs par défaut
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>

    <!-- Modal de sauvegarde -->
    <div x-show="showBackupModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sauvegarde système</h3>
                <div class="space-y-3">
                    <button @click="createBackup('database')" 
                            class="w-full text-left px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="font-medium">Base de données</div>
                        <div class="text-sm text-gray-500">Sauvegarder uniquement les données</div>
                    </button>
                    <button @click="createBackup('full')" 
                            class="w-full text-left px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="font-medium">Sauvegarde complète</div>
                        <div class="text-sm text-gray-500">Base de données + fichiers</div>
                    </button>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="showBackupModal = false" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function settingsManager() {
    return {
        activeTab: 'general',
        showBackupModal: false,
        
        exportSettings() {
            alert('Export de la configuration en cours...');
        },
        
        createBackup(type) {
            alert(`Création d'une sauvegarde ${type} en cours...`);
            this.showBackupModal = false;
        },
        
        resetToDefaults() {
            if (confirm('Êtes-vous sûr de vouloir restaurer les valeurs par défaut ?')) {
                // Logique pour restaurer les valeurs par défaut
                alert('Valeurs par défaut restaurées');
            }
        }
    }
}
</script>
@endsection