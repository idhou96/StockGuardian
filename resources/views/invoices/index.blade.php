{{-- resources/views/invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Factures')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Factures', 'url' => null]
            ]" />
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestion des Factures</h1>
                    <p class="mt-1 text-sm text-gray-600">Gérez la facturation et les paiements clients</p>
                </div>
                @can('create', App\Models\Invoice::class)
                    <a href="{{ route('invoices.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouvelle Facture
                    </a>
                @endcan
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Factures</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Attente</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Payées</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['paid'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} F</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
            </div>
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client</label>
                        <select name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous les clients</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous les statuts</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payée</option>
                            <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partiellement payée</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>En retard</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date début</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date fin</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Référence</label>
                        <input type="text" name="reference" value="{{ request('reference') }}" 
                               placeholder="FAC..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table des factures --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Liste des Factures</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('invoices.export') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $invoice->reference }}</div>
                                    @if($invoice->sale_id)
                                        <div class="text-sm text-gray-500">
                                            <a href="{{ route('sales.show', $invoice->sale) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Vente: {{ $invoice->sale->reference }}
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $invoice->customer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $invoice->customer->phone ?? $invoice->customer->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $invoice->invoice_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($invoice->due_date)
                                        <span class="{{ $invoice->due_date->isPast() && !$invoice->isPaid() ? 'text-red-600 font-medium' : '' }}">
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                        </span>
                                        @if($invoice->due_date->isPast() && !$invoice->isPaid())
                                            <div class="text-xs text-red-500">{{ $invoice->due_date->diffForHumans() }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Non définie</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} F</div>
                                    @if($invoice->discount_amount > 0)
                                        <div class="text-xs text-gray-500">Remise: {{ number_format($invoice->discount_amount, 0, ',', ' ') }} F</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} F</div>
                                    @if($invoice->remaining_amount > 0)
                                        <div class="text-xs text-red-500">Reste: {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} F</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'draft' => 'gray',
                                            'sent' => 'blue',
                                            'paid' => 'green',
                                            'partially_paid' => 'yellow',
                                            'overdue' => 'red',
                                            'cancelled' => 'red'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Brouillon',
                                            'sent' => 'Envoyée',
                                            'paid' => 'Payée',
                                            'partially_paid' => 'Partiellement payée',
                                            'overdue' => 'En retard',
                                            'cancelled' => 'Annulée'
                                        ];
                                        $color = $statusColors[$invoice->status] ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('invoices.show', $invoice) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Voir
                                        </a>
                                        @can('update', $invoice)
                                            @if($invoice->status === 'draft')
                                                <a href="{{ route('invoices.edit', $invoice) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Modifier
                                                </a>
                                            @endif
                                        @endcan
                                        <a href="{{ route('invoices.pdf', $invoice) }}" 
                                           class="text-purple-600 hover:text-purple-900">
                                            PDF
                                        </a>
                                        @can('send-email', $invoice)
                                            @if(in_array($invoice->status, ['draft', 'sent']))
                                                <button onclick="sendInvoiceEmail({{ $invoice->id }})" 
                                                        class="text-green-600 hover:text-green-900">
                                                    Envoyer
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Aucune facture trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $invoices->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal d'envoi d'email --}}
<div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Envoyer la facture par email</h3>
            <form id="emailForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email du destinataire</label>
                    <input type="email" name="email" id="recipientEmail" required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                    <textarea name="message" rows="3" 
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Message personnalisé à joindre à la facture..."></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeEmailModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendInvoiceEmail(invoiceId) {
    // Pré-remplir l'email du client si disponible
    fetch(`/invoices/${invoiceId}?format=json`)
        .then(response => response.json())
        .then(data => {
            if (data.customer && data.customer.email) {
                document.getElementById('recipientEmail').value = data.customer.email;
            }
            document.getElementById('emailForm').action = `/invoices/${invoiceId}/send-email`;
            document.getElementById('emailModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('emailForm').action = `/invoices/${invoiceId}/send-email`;
            document.getElementById('emailModal').classList.remove('hidden');
        });
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
    document.getElementById('emailForm').reset();
}

// Gestion du formulaire d'envoi d'email
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Envoi en cours...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEmailModal();
            location.reload();
        } else {
            alert('Erreur lors de l\'envoi de l\'email');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'envoi de l\'email');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Envoyer';
    });
});
</script>
@endpush
@endsection
