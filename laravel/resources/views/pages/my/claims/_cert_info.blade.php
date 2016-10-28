<style>
    table.certificate-info th {
        border-bottom: 0.2em solid #959595;
        font-weight: normal;
        margin-left: 2pt;
        width: 35%;
        font-size:13pt;
        color:#262626;
    }
    table.certificate-info td {
        border-bottom: 0.2em solid #959595;
        font-weight: bold;
        width:63%;
        font-size:13pt;
        color:#000;
    }
</style>

<br><br>

<table cellspacing="5" cellpadding="5" class="certificate-info" width="100%">
    <tr>
        <th>Issued To</th>
        <td>{{ $product->manufacturer }}</td>
    </tr>
    <tr>
        <th>Product</th>
        <td><a href="{{ getSiteUrl() }}/product/{{ $product->slug }}">{{ $product->full_name }}</a></td>
    </tr>
    <tr>
        <th>Product Version</th>
        <td>{{ $product->version }}</td>
    </tr>
    <tr>
        <th>Test Suite</th>
        <td><a href="{{ getSiteUrl() }}/test-suite/{{ $testSuite->slug }}">{{ $testSuite->name }}</a></td>
    </tr>
    <tr>
        <th>Test Suite Version</th>
        <td>{{ $testSuite->version_major . '.' . $testSuite->version_minor }}</td>
    </tr>
    <tr>
        <th>Specification Issuer</th>
        <td>{{ $testSuite->issuer }}</td>
    </tr>
    <tr>
        <th>Conformance Level</th>
        <td>{{ $claim->conformance_level }}</td>
    </tr>
    <tr>
        <th>Role</th>
        <td>{{ $claim->role }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ $claim->status }}</td>
    </tr>
    <tr>
        <th>Claim ID</th>
        <td>{{ $claim->id }}</td>
    </tr>
    <tr>
        <th>Date of Claim</th>
        <td>{{ $claim->created_at->format('d F Y') }}</td>
    </tr>
    @if($passCount)
        <tr>
            <th>Test Cases Passed</th>
            <td>{{ $passCount }} of {{ $totalCount }}</td>
        </tr>
    @endif
    @if($skipCount)
        <tr>
            <th>Test Cases Skipped</th>
            <td>{{ $skipCount }} of {{ $totalCount }}</td>
        </tr>
    @endif
    @if($excludeCount)
        <tr>
            <th>Test Cases Excluded</th>
            <td>{{ $excludeCount }} of {{ $totalCount }}</td>
        </tr>
    @endif
</table>