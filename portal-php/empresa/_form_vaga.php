<div class="mb-3">
    <label class="form-label fw-bold">Título <span class="text-danger">*</span></label>
    <input type="text" name="titulo" class="form-control"
           placeholder="Ex: Estágio em Desenvolvimento Web" required>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Descrição <span class="text-danger">*</span></label>
    <textarea name="descricao" class="form-control" rows="3"
              placeholder="Descreva as atividades..." required></textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Área</label>
        <input type="text" name="area" class="form-control" placeholder="Ex: Desenvolvimento Web">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Local</label>
        <input type="text" name="local" class="form-control" placeholder="Ex: Umuarama - PR">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Carga horária (h/sem)</label>
        <input type="number" name="carga_horaria" class="form-control"
               placeholder="Ex: 20" min="1" max="40">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Bolsa (R$)</label>
        <input type="number" name="bolsa" class="form-control"
               placeholder="Ex: 800.00" step="0.01" min="0">
    </div>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Requisitos</label>
    <input type="text" name="requisitos" class="form-control"
           placeholder="Ex: HTML, CSS, JavaScript básico">
</div>
