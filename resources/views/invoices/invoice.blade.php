<style>
    body {
        font-size: {{ $template->settings['font_size'] ?? '11px' }};
        font-family: DejaVu Sans, sans-serif;
    }

    {!! $template->template_css !!}
</style>

{!! Blade::render($template->template_html, [
    'invoice' => $invoice,
    'order'   => $order,
    'product' => $product,
    'settings'=> $settings,
    'logoPath'=> $logoPath
]) !!}
