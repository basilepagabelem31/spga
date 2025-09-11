@extends('layouts.app')

@section('title', 'Détails de la commande #' . $order->id)

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Détails de la Commande #{{ $order->id }}</h1>
    <p class="mb-4">Informations complètes sur votre commande.</p>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Informations sur la commande
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Code de commande:</dt>
                        <dd class="col-sm-8">{{ $order->order_code }}</dd>

                        <dt class="col-sm-4">Date de commande:</dt>
                        <dd class="col-sm-8">{{ $order->order_date->format('d/m/Y H:i') }}</dd>
                        
                        <dt class="col-sm-4">Statut:</dt>
                        <dd class="col-sm-8">
                            @php
                                $badgeClass = '';
                                switch($order->status) {
                                    case 'En attente de validation':
                                    case 'En préparation':
                                        $badgeClass = 'bg-warning text-dark';
                                        break;
                                    case 'Validée':
                                    case 'En livraison':
                                        $badgeClass = 'bg-info';
                                        break;
                                    case 'Livrée':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'Annulée':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    default:
                                        $badgeClass = 'bg-secondary';
                                        break;
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                        </dd>

                        <dt class="col-sm-4">Montant total:</dt>
                        <dd class="col-sm-8 fw-bold text-success">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list-ul me-1"></i> Articles de la commande
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }} {{ $item->sale_unit_at_order }}</td>
                                        <td>{{ number_format($item->unit_price_at_order, 2, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($item->quantity * $item->unit_price_at_order, 2, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-truck me-1"></i> Détails de livraison
                </div>
                <div class="card-body">
                    <p>Mode de livraison: <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span></p>
                    <p>Mode de paiement: <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $order->payment_mode)) }}</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('client.orders') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Retour à mes commandes</a>
    </div>
</div>
@endsection