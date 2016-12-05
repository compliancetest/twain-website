<thead>
<tr>
    <th>Claim ID</th>
    <th>Certificate</th>
    <th>Issuer</th>
    <th>Suite</th>
    <th>Level</th>
    <th>Role</th>
    <th>Status</th>
    <th>Date</th>
    @can('change', $product)
        <th>Action</th>
    @endcan
</tr>
</thead>
<tbody>
@if(count($product->claims))
    @foreach($product->claims()->orderBy('created_at')->get() as $claim)
        <tr id="claimRow{{ $claim->id }}">
            <td>{{ $claim->id }}</td>
            <td class="text-center">
                <a href="{{ $claim->getPdfUrl() }}" class="btn btn-primary btn-icon btn-view"
                   onclick="window.open('{{ $claim->getPdfUrl() }}', '', 'height=600');return false;" data-tooltip="tooltip" title="View">View</a>
                <a href="{{ $claim->getPdfUrl() }}" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip"
                   title="Download">Download</a>
            </td>
            <td>{{ $claim->testSuite->issuer }}</td>
            <td><a href="/test-suite/{{ $claim->testSuite->slug }}">{{ $claim->testSuite->full_name }}</a></td>
            <td class="text-center">{{ $claim->conformance_level }}</td>
            <td class="text-center">{{ $claim->role }}</td>
            <td class="text-center"><span class="text-status-verified">Verified</span></td>
            <td class="text-center">{{ formatDate($claim->created_at) }}</td>
            @can('change', $product)
                <td class="text-center">
                    <a href="#deleteProductClaimModal{{ $claim->id }}" class="btn btn-danger btn-icon btn-delete" data-toggle="modal" data-tooltip="tooltip" title="Delete Claim">Delete</a>
                    @include('pages.products.partials.confirm-delete-claim-modal', ['id' => $claim->id, 'productSlug' => $product->slug])
                </td>
            @endcan
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="@can('change', $product) 9 @else 8 @endcan" class="empty-row">No compliance claim recorded yet</td>
    </tr>
@endif
</tbody>