<div dir="rtl" style="font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:24px;border:1px solid #e5e7eb;border-radius:8px">

    <h2 style="color:{{ $color }}">{{ $heading }}</h2>

    <p>{!! $intro !!}</p>

    @if(!empty($details))
        <div style="background:{{ $backgroundColor }};border-right:4px solid {{ $color }};padding:12px 16px;border-radius:4px;margin:16px 0">
            @foreach($details as $label => $value)
                <p style="margin:{{ $loop->last ? '0' : '0 0 8px' }}">
                    {{ $label }}: <strong>{{ $value }}</strong>
                </p>
            @endforeach
        </div>
    @endif

    @if($extraHtml)
        {!! $extraHtml !!}
    @endif

    @if($actionText && $actionUrl)
        <p style="margin:24px 0">
            <a href="{{ $actionUrl }}"
               style="background:{{ $color }};color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block">
                {{ $actionText }}
            </a>
        </p>
    @endif

    <p style="color:#6b7280;font-size:13px">مع تحيات فريق المنصة</p>
</div>
