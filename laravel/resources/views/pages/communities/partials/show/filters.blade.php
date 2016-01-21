<div class="expandable">
    <h6 class="exp_title">{{ $filterName }}</h6>

    <div class="exp_content">
        @foreach($filterData as $k => $v)

            <label for="{{ $k }}" class="blue_txt">
                <input type="checkbox" name="{{ $searchType }}[]" value="{{ $k }}" id="{{ $k }}"
                       @if(in_array($k, $selecteFilters)) checked="checked" @endif class="input_filter"> {{ $k }}
                ({{ $v }})
            </label>

            <div class="clear"></div>

        @endforeach

    </div>
</div>