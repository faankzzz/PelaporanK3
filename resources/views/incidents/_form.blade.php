@csrf

{{-- Info Otomatis: Waktu & Pelapor --}}
<div class="autofill-banner mb-3">
    <div class="row align-items-center g-2">
        <div class="col-md-7">
            <div class="small fw-bold text-dark">
                <i class="bi bi-clock me-1 text-primary"></i> Waktu Kejadian:
                <span id="liveTimestampText" class="badge text-bg-light border text-dark ms-1">Memuat waktu...</span>
            </div>
            <div class="text-muted" style="font-size: 0.775rem;">
                Tanggal dan jam tercatat otomatis saat pelaporan ini dibuat.
            </div>
            <input type="hidden" name="tanggal_kejadian" id="tanggal_kejadian_input"
                   value="{{ old('tanggal_kejadian', isset($incident->tanggal_kejadian) ? $incident->tanggal_kejadian->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="col-md-5 text-md-end">
            <div class="small text-muted">Pelapor:</div>
            <div class="fw-semibold text-dark small">
                {{ auth()->user()->name }}
                <span class="text-muted">(NIP: {{ auth()->user()->nip ?: '-' }})</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Unit & Jenis Insiden --}}
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Unit / Area Pabrik <span class="text-danger">*</span></label>
        <select name="unit_id" id="unit_id_select" class="form-select form-select-sm" required>
            <option value="">-- Pilih Unit / Area --</option>
            @php $userUnitId = auth()->user()->unit_id; @endphp
            @foreach($units as $u)
                <option value="{{ $u->id }}" @selected(old('unit_id', $incident->unit_id ?? $userUnitId) == $u->id)>
                    {{ $u->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-semibold">Jenis Kejadian <span class="text-danger">*</span></label>
        <select name="incident_type_id" id="incident_type_select" class="form-select form-select-sm" required>
            <option value="">-- Pilih Jenis --</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}" @selected(old('incident_type_id', $incident->incident_type_id ?? '') == $t->id)>
                    {{ $t->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label small fw-semibold">Lokasi Spesifik / Titik Kejadian <span class="text-danger">*</span></label>
        <input type="text" name="lokasi_detail" id="lokasi_detail_input" class="form-control form-control-sm"
               placeholder="Contoh: Lantai 2 Pabrik Pusri-IIB, dekat Kompresor Gas 101-J"
               value="{{ old('lokasi_detail', $incident->lokasi_detail ?? '') }}" required>
    </div>

    {{-- Fokus Utama: Kronologi Kejadian --}}
    <div class="col-12">
        <label class="form-label small fw-semibold">Deskripsi Kronologi Kejadian <span class="text-danger">*</span></label>
        <textarea name="kronologi" rows="4" class="form-control"
                  placeholder="Jelaskan secara detail apa yang terjadi, aktivitas yang sedang berlangsung, dan kondisi di tempat kejadian..."
                  required>{{ old('kronologi', $incident->kronologi ?? '') }}</textarea>
    </div>

    {{-- KAMERA BUKTI LANGSUNG --}}
    <div class="col-12">
        <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-camera me-1"></i> Foto Bukti Langsung Dari Kamera</span>
            <span class="text-muted" style="font-size: 0.75rem;">(Hanya pengambilan langsung dari kamera perangkat)</span>
        </label>

        {{-- Preview jika sudah ada foto sebelumnya (mode edit) --}}
        @if(!empty($incident->foto))
            <div class="mb-2 p-2 bg-white rounded border">
                <span class="small text-muted d-block mb-1">Foto tersimpan saat ini:</span>
                <img src="{{ asset('storage/' . $incident->foto) }}" class="rounded img-fluid" style="max-height: 160px;">
            </div>
        @endif

        {{-- Area Kamera Live --}}
        <div id="cameraArea" class="camera-container text-center mb-2">
            <div class="camera-overlay">
                <span class="camera-badge">
                    <i class="bi bi-circle-fill text-danger me-1" style="font-size: 0.6rem;"></i> Kamera Siap
                </span>
                <span class="camera-badge" id="cameraTimestampBadge">
                    {{ now()->format('d/m/Y H:i:s') }}
                </span>
            </div>

            <video id="cameraVideo" class="camera-viewfinder" autoplay playsinline muted></video>

            <div id="cameraPlaceholder" class="p-4 text-white" style="display: none;">
                <i class="bi bi-camera-video-off fs-2 text-warning mb-2 d-block"></i>
                <div class="fw-semibold small">Kamera Belum Aktif</div>
                <p class="text-muted small mb-2" style="font-size: 0.8rem;">Izinkan akses kamera di browser Anda atau gunakan tombol di bawah:</p>
                <button type="button" class="btn btn-pusri-yellow btn-sm" onclick="startCamera()">
                    Nyalakan Kamera
                </button>
            </div>

            <div class="camera-controls">
                <button type="button" class="btn btn-sm btn-outline-light" onclick="switchCamera()" title="Putar Kamera">
                    <i class="bi bi-arrow-repeat"></i> Putar Kamera
                </button>
                <button type="button" class="btn-capture" onclick="takeSnapshot()">
                    <i class="bi bi-camera-fill text-primary"></i> Ambil Foto
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="triggerNativeCamera()" title="Kamera Bawaan">
                    <i class="bi bi-phone"></i> Kamera HP
                </button>
            </div>
        </div>

        {{-- Preview Hasil Jepretan --}}
        <div id="previewArea" style="display: none;" class="mb-2">
            <div class="captured-preview-box">
                <div class="watermark-banner d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-check-circle me-1 text-success"></i> Foto Bukti Berhasil Diambil</span>
                    <span class="badge bg-secondary" style="font-size: 0.7rem;">TERVALIDASI</span>
                </div>
                <img id="capturedImage" class="img-fluid w-100" style="max-height: 320px; object-fit: contain;">
            </div>
            <div class="mt-2 d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="retakePhoto()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ambil Ulang Foto
                </button>
            </div>
        </div>

        <canvas id="snapshotCanvas" style="display: none;"></canvas>
        <input type="file" name="foto" id="fotoInput" accept="image/*" capture="environment" style="display: none;">
    </div>

    {{-- Korban & Saksi (Opsional) --}}
    <div class="col-12">
        <div class="card card-pusri">
            <div class="card-header py-1.5" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#tambahanCollapse">
                <span class="small fw-semibold text-secondary">
                    + Korban &amp; Saksi (Opsional - Klik jika ada)
                </span>
            </div>
            <div class="collapse" id="tambahanCollapse">
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Nama Korban (jika ada)</label>
                            <input type="text" name="korban" class="form-control form-control-sm"
                                   placeholder="Nama karyawan / kontraktor yang cedera"
                                   value="{{ old('korban', $incident->korban ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Nama Saksi di Lokasi</label>
                            <input type="text" name="saksi" class="form-control form-control-sm"
                                   placeholder="Nama rekan kerja yang melihat kejadian"
                                   value="{{ old('saksi', $incident->saksi ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Sinkronisasi Waktu
    function updateLiveClock() {
        const now = new Date();
        const options = {
            day: 'numeric', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        const formatted = now.toLocaleDateString('id-ID', options) + ' WIB';
        const liveBadge = document.getElementById('liveTimestampText');
        const camBadge = document.getElementById('cameraTimestampBadge');
        if (liveBadge) liveBadge.textContent = formatted;
        if (camBadge) camBadge.textContent = now.toLocaleTimeString('id-ID');

        const inputDate = document.getElementById('tanggal_kejadian_input');
        if (inputDate && !inputDate.value) {
            const pad = (n) => String(n).padStart(2, '0');
            inputDate.value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        }
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    // 2. Kamera WebRTC & Canvas
    let videoStream = null;
    let currentFacingMode = "environment";

    const video = document.getElementById('cameraVideo');
    const placeholder = document.getElementById('cameraPlaceholder');
    const cameraArea = document.getElementById('cameraArea');
    const previewArea = document.getElementById('previewArea');
    const capturedImage = document.getElementById('capturedImage');
    const canvas = document.getElementById('snapshotCanvas');
    const fileInput = document.getElementById('fotoInput');

    async function startCamera() {
        stopCamera();
        placeholder.style.display = 'none';
        video.style.display = 'block';

        try {
            videoStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            video.srcObject = videoStream;
            await video.play();
        } catch (err) {
            video.style.display = 'none';
            placeholder.style.display = 'block';
        }
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    function switchCamera() {
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        startCamera();
    }

    function triggerNativeCamera() {
        fileInput.click();
    }

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() { burnWatermarkAndAttach(img); };
                img.src = event.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    function takeSnapshot() {
        if (!videoStream || video.videoWidth === 0) {
            triggerNativeCamera();
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        burnWatermarkAndAttach(null);
    }

    function burnWatermarkAndAttach(sourceImg) {
        const ctx = canvas.getContext('2d');
        if (sourceImg) {
            canvas.width = sourceImg.width;
            canvas.height = sourceImg.height;
            ctx.drawImage(sourceImg, 0, 0);
        }

        const width = canvas.width;
        const height = canvas.height;
        const unitSelect = document.getElementById('unit_id_select');
        const unitName = unitSelect && unitSelect.selectedIndex > 0 ? unitSelect.options[unitSelect.selectedIndex].text.trim() : 'Area Pabrik Pusri';
        const lokasi = document.getElementById('lokasi_detail_input').value.trim() || 'Lokasi Kejadian';
        const now = new Date();
        const timeStr = now.toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        }) + ' WIB';

        // Watermark bar bawah
        const barHeight = Math.max(48, Math.round(height * 0.10));
        ctx.fillStyle = 'rgba(0, 38, 77, 0.90)';
        ctx.fillRect(0, height - barHeight, width, barHeight);

        // Garis aksen kuning
        ctx.fillStyle = '#F5A623';
        ctx.fillRect(0, height - barHeight, width, Math.max(3, Math.round(barHeight * 0.07)));

        // Teks Watermark
        ctx.fillStyle = '#FFFFFF';
        ctx.font = `bold ${Math.max(12, Math.round(barHeight * 0.30))}px sans-serif`;
        ctx.fillText(`K3 PT PUPUK SRIWIDJAJA PALEMBANG`, 12, height - (barHeight * 0.55));

        ctx.fillStyle = '#E2E8F0';
        ctx.font = `${Math.max(10, Math.round(barHeight * 0.22))}px sans-serif`;
        ctx.fillText(`${timeStr} | ${unitName} - ${lokasi}`, 12, height - (barHeight * 0.20));

        capturedImage.src = canvas.toDataURL('image/jpeg', 0.90);
        cameraArea.style.display = 'none';
        previewArea.style.display = 'block';
        stopCamera();

        canvas.toBlob((blob) => {
            const file = new File([blob], `k3_pusri_${Date.now()}.jpg`, { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }, 'image/jpeg', 0.90);
    }

    function retakePhoto() {
        previewArea.style.display = 'none';
        cameraArea.style.display = 'block';
        fileInput.value = "";
        startCamera();
    }

    window.addEventListener('DOMContentLoaded', () => {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            startCamera();
        } else {
            placeholder.style.display = 'block';
            video.style.display = 'none';
        }
    });

    window.addEventListener('beforeunload', stopCamera);
</script>
@endpush
