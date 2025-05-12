{{-- General stylesheet for PDF templates --}}
<style>
    {!! file_get_contents(public_path('build/assets/pdf.css')) !!}
    @if(!empty($watermark))
    body::after {
        content: "{{ $watermark }}";
        position: fixed;
        top: 40%;
        left: 30%;
        font-size: 10em;
        color: rgba(0,0,0,0.1);
        transform: rotate(-30deg);
        pointer-events: none;
        z-index: 9999;
    }
    @endif
</style>