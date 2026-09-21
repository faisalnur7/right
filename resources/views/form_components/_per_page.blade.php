<label for="per_page" class="form-label">Per page</label>
<select name="per_page" onchange="this.form.submit()" id="per_page" class="form-control">
    @for ($i = 10; $i <= 100; $i += 10)
        <option value="{{ $i }}" {{ request('per_page') == $i ? 'selected' : '' }}>
            {{ $i }}
        </option>
    @endfor
</select>
