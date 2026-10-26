@extends('layouts.superadmin')

@section('title', 'Plan prices')
@section('heading', 'Plan prices')
@section('subheading', 'Set INR and USD for every public plan. Checkout currently charges INR.')

@section('content')
<section class="card">
    <p class="help">
        Fill both currencies for each row. Virtual plan totals still use the same formulas (couple multiplier, half-yearly 20% package discount).
        Leave USD at 0 until you are ready — the site will keep showing rupees for that amount.
    </p>

    <form method="POST" action="{{ route('superadmin.prices.update') }}" style="margin-top:1.4rem">
        @csrf
        @method('PUT')

        <div class="price-groups">
            @foreach ($rows as $group => $items)
                <article>
                    <div class="card-head">
                        <h3>{{ $group }}</h3>
                    </div>
                    <div class="table-scroll">
                        <table class="price-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>INR (₹)</th>
                                    <th>USD ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="td-main">{{ $item['label'] }}</div>
                                            <div class="td-sub">{{ $item['key'] }}</div>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="1" name="prices[{{ $item['key'] }}][amount_inr]" value="{{ old('prices.'.$item['key'].'.amount_inr', $item['amount_inr']) }}" required>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.01" name="prices[{{ $item['key'] }}][amount_usd]" value="{{ old('prices.'.$item['key'].'.amount_usd', number_format($item['amount_usd'], 2, '.', '')) }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="form-actions" style="margin-top:1.4rem">
            <button type="submit" class="btn-gold">Save prices</button>
        </div>
    </form>
</section>
@endsection
