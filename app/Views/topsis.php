<div class="container">
    <h2 class="mb-4">📈 Metode TOPSIS</h2>
    
    <div class="alert alert-success">
        <strong>Cara Kerja:</strong> TOPSIS mencari alternatif yang paling dekat dengan solusi ideal positif dan paling jauh dari solusi ideal negatif.
    </div>

    <form method="post" action="<?= base_url('topsis/calculate') ?>" id="topsisForm">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">1. Input Jumlah Data</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Alternatif:</label>
                        <input type="number" class="form-control" id="numAlternatives" min="2" max="10" value="3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Kriteria:</label>
                        <input type="number" class="form-control" id="numCriteria" min="2" max="10" value="3">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="generateInputs()">Generate Form</button>
            </div>
        </div>

        <div id="dynamicInputs"></div>

        <button type="submit" class="btn btn-success btn-lg w-100" id="calculateBtn" style="display:none;">
            Hitung Hasil TOPSIS
        </button>
    </form>

    <?php if (isset($results)): ?>
    <div class="result-card mt-5">
        <h3 class="mb-4">📊 Hasil Perhitungan TOPSIS</h3>
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Peringkat Alternatif (Preferensi Relatif)</h5>
            </div>
            <div class="card-body">
                <?php foreach ($results as $index => $result): ?>
                <div class="p-3 mb-2 rounded <?= $index < 3 ? 'rank-'.($index+1) : 'bg-light' ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <span class="badge bg-dark me-2">#<?= $index + 1 ?></span>
                                <?= esc($result['name']) ?>
                            </h5>
                        </div>
                        <div>
                            <h4 class="mb-0"><?= number_format($result['score'], 4) ?></h4>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function generateInputs() {
    const numAlts = parseInt(document.getElementById('numAlternatives').value);
    const numCrits = parseInt(document.getElementById('numCriteria').value);
    
    let html = '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">2. Nama Alternatif</h5></div><div class="card-body"><div class="row">';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<div class="col-md-4 mb-3">
            <label class="form-label">Alternatif ${i+1}:</label>
            <input type="text" name="alternatives[]" class="form-control" placeholder="Contoh: Smartphone A" required>
        </div>`;
    }
    html += '</div></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-secondary text-white"><h5 class="mb-0">3. Kriteria, Bobot, dan Tipe</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead><tbody>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<tr>
            <td><input type="text" name="criteria[]" class="form-control" placeholder="Contoh: Harga" required></td>
            <td><input type="number" name="weights[]" class="form-control" step="0.01" min="0" value="0.2" required></td>
            <td>
                <select name="criteria_types[]" class="form-select" required>
                    <option value="benefit">Benefit (Semakin Tinggi Semakin Baik)</option>
                    <option value="cost">Cost (Semakin Rendah Semakin Baik)</option>
                </select>
            </td>
        </tr>`;
    }
    html += '</tbody></table></div><small class="text-muted">*Total bobot sebaiknya = 1</small></div></div>';
    
    html += '<div class="card mb-4"><div class="card-header bg-info text-white"><h5 class="mb-0">4. Nilai Alternatif untuk Setiap Kriteria</h5></div><div class="card-body">';
    html += '<div class="table-responsive"><table class="table table-bordered"><thead class="table-light"><tr><th>Alternatif</th>';
    
    for (let i = 0; i < numCrits; i++) {
        html += `<th>Kriteria ${i+1}</th>`;
    }
    html += '</tr></thead><tbody>';
    
    for (let i = 0; i < numAlts; i++) {
        html += `<tr><th class="table-light">Alt. ${i+1}</th>`;
        for (let j = 0; j < numCrits; j++) {
            html += `<td><input type="number" name="matrix[${i}][${j}]" class="form-control" step="0.01" value="0" required></td>`;
        }
        html += '</tr>';
    }
    html += '</tbody></table></div></div></div>';
    
    document.getElementById('dynamicInputs').innerHTML = html;
    document.getElementById('calculateBtn').style.display = 'block';
}
</script>