<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? '' }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;">

<div style="max-width:600px;margin:40px auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">

    {{-- Header --}}
    <div style="background:#1d4ed8;padding:24px 32px;">
        <h1 style="margin:0;color:#ffffff;font-size:20px;">
            {{ config('app.name') }}
        </h1>
    </div>

    {{-- Body --}}
    <div style="padding:32px;">

        @foreach($lines as $line)
            <p style="margin:0 0 16px;color:#374151;font-size:15px;line-height:1.6;">
                {{ $line }}
            </p>
        @endforeach

        {{-- Action Button --}}
        @if($actionText && $actionUrl)
            <div style="margin:32px 0;text-align:center;">
                <a href="{{ $actionUrl }}"
                   style="display:inline-block;background:#1d4ed8;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:6px;font-size:15px;font-weight:bold;">
                    {{ $actionText }}
                </a>
            </div>

            {{-- Fallback URL --}}
            <p style="margin:16px 0 0;color:#9ca3af;font-size:12px;text-align:center;">
                If the button doesn't work, copy this link:
                <br>
                <a href="{{ $actionUrl }}" style="color:#1d4ed8;word-break:break-all;">
                    {{ $actionUrl }}
                </a>
            </p>
        @endif

    </div>

    {{-- Footer --}}
    <div style="background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;">
        <p style="margin:0;color:#9ca3af;font-size:12px;text-align:center;">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</div>

</body>
</html>
