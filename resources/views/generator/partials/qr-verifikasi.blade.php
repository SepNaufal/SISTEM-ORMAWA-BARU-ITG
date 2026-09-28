@php
    /** @var \App\Models\TandaTanganDigital $signature */
    $qrSize = $qrSize ?? 80;
    $qrDataUri = \App\Services\DigitalSignatureService::generateQrCodeDataUri($signature->verification_url, $qrSize);
@endphp
{{-- Blok QR + keterangan, disejajarkan tengah. <img> dibuat inline agar
     perataan tengah dihormati baik oleh browser maupun DomPDF. --}}
<div style="text-align: center; margin-top: 2px; line-height: 1.3;">
    <img src="{{ $qrDataUri }}" width="{{ $qrSize }}" height="{{ $qrSize }}" alt="QR Verifikasi Tanda Tangan" style="display: inline; width: {{ $qrSize }}px; height: {{ $qrSize }}px;">
    <div style="font-weight: 700; color: #1e1b4b; text-transform: uppercase; font-size: 6.5pt; margin-top: 3px;">Ditandatangani Secara Elektronik</div>
    <div style="font-family: monospace; font-size: 7pt; color: #475569;">{{ $signature->token_verifikasi }}</div>
    <div style="font-size: 6.5pt; color: #475569;">{{ $signature->signed_at?->format('d/m/Y') }}</div>
    <div style="color: #166534; font-weight: 700; font-size: 6.5pt;">ASLI &amp; SAH</div>
</div>
