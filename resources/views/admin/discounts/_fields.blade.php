<div class="form-group">
    <label class="form-label">Kod (np. SER10)</label>
    <input type="text" name="code" value="{{ old('code', $discount?->code) }}" class="form-input">
</div>

<div class="form-group">
    <label class="form-label">Typ rabatu</label>
    <select name="type" class="form-select">
        <option value="percent" @selected(old('type', $discount?->type) === 'percent')>Procentowy (%)</option>
        <option value="fixed" @selected(old('type', $discount?->type) === 'fixed')>Kwotowy (grosze)</option>
    </select>
</div>

<div class="form-group">
    <label class="form-label">Wartość</label>
    <input type="number" name="value" min="1" value="{{ old('value', $discount?->value) }}" class="form-input">
    <small class="muted">Procentowy: liczba 1–100. Kwotowy: w groszach (np. 1500 = 15 zł).</small>
</div>

<div class="form-group">
    <label class="form-label">Minimalna wartość koszyka (w groszach, opcjonalnie)</label>
    <input type="number" name="min_cart_value" min="0" value="{{ old('min_cart_value', $discount?->min_cart_value) }}" class="form-input">
</div>

<div class="form-group">
    <label class="form-label">Limit użyć (opcjonalnie, puste = bez limitu)</label>
    <input type="number" name="max_uses" min="1" value="{{ old('max_uses', $discount?->max_uses) }}" class="form-input">
</div>

<div class="form-group">
    <label class="form-label">Ważny do (opcjonalnie, puste = bezterminowy)</label>
    <input type="date" name="expires_at" value="{{ old('expires_at', $discount?->expires_at?->format('Y-m-d')) }}" class="form-input">
</div>

<label class="form-check">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $discount?->is_active ?? true))>
    <span class="muted">Kod aktywny</span>
</label>
