@if(!empty($notify))
    @foreach($notify as $item)
        <div class="dropdown-divider"></div>
        <a class="dropdown-item notify_item" href="#" data-id="{{$item->order_id}}">
            <span class="text-danger">
                <i class="far fa-envelope"></i>
                <strong>{{$item->client_name}}</strong>
            </span>
            <span class="small float-right text-muted">{{$item->date}}</span>
            <div class="dropdown-message small">
                @if($item->confirmed != 1)
                    Զեղչված ապրանք <br>
                @endif
                @if($item->not_enough != null)
                    Անբավարար ապրանք
                @endif
            </div>
        </a>
    @endforeach
@endif