{{-- Memuat public/dist/css/custom.css langsung ke dalam HTML (inline), karena DOMPDF sering gagal memuat CSS lewat URL. --}}
<style>{!! file_get_contents(public_path('dist/css/custom.css')) !!}</style>
