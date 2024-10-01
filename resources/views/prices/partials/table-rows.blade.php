@foreach($prices as $price)
    <tr id="priceRow_{{ $price->number }}">
        <td>{{ $price->number }}</td>
        <td>{{ $price->name }}</td>
        <td>{{ $price->bottlesize / 1000 . ' L' }}</td>
        <td id="price_{{ $price->number }}">{{ $price->price * ($price->orderamount ?: 1) . ' €'}}</td>
        <td id="priceGBP_{{ $price->number }}">{{ $price->priceGBP * ($price->orderamount ?: 1) . ' £'}}</td>
        <td id="orderAmount_{{ $price->number }}">{{ $price->orderamount }}</td>
        <td>
            <button data-number="{{ $price->number }}" type="button" class="btn btn-success add-btn">ADD</button>
            <button data-number="{{ $price->number }}" type="button" class="btn btn-danger clear-btn">CLEAR</button>
        </td>
    </tr>
@endforeach
