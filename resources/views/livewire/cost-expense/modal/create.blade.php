<form>
    <x-input-text type="datetime-local" id="date" name="date" title="Expense Date" />
    <x-input-select id="cost_id" name="cost_id" title="Cost Name" placeholder="Select Cost" :options="$costs" />
    <x-textarea-input id="desc" name="desc" title="Description" />
    <div class="grid grid-cols-2 gap-4">
        <x-input-text type="number" id="amount" name="amount" title="Expense Amount" prepend="Rp"/>
        <x-input-text type="number" id="qty" name="qty" title="Quantity"/>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <x-input-text id="uom" name="uom" title="Uom" placeholder="Unit"/>
        <x-input-text type="number" id="total_amount" name="total_amount" title="Total Amount" disabled/>
    </div>
</form>
