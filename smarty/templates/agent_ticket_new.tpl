<div class="card">
	<div class="card-header">
		<h3 class="card-title">Neues Ticket anlegen</h3>
	</div>

	<form id="frmNewTicket" class="form card-body" method="post" action="#">
	<div class="mb-10">
			<label class="required form-label">Betreff</label>
			<input type="text" name="subject" class="form-control" placeholder="Ticket-Betreff" required />
		</div>

		<div class="mb-10">
			<label class="required form-label">Ticket-Kategorie</label>
			<select name="category" class="form-select" data-control="select2" data-placeholder="Kategorie wählen" required>
			</select>
		</div>

		<div class="mb-10">
			<label class="required form-label">Melder</label>
			<select name="reportee" class="form-select" data-control="select2" data-placeholder="Melder wählen" required>
			</select>
		</div>

		<div class="mb-10">
		<label class="form-label">Ticket-Bearbeiter</label>
			<select name="owner" class="form-select" data-control="select2" data-placeholder="Bearbeiter wählen">
			</select>
			<span class="form-text">Kann leer bleiben, wenn noch kein Bearbeiter feststeht.</span>
		</div>

		<div class="mb-10">
			<label class="form-label">Weitere verbundene Personen (Melder, Stakeholder, ...)</label>
			<select name="assignees[]" class="form-select" multiple data-placeholder="Person(en) auswählen">
			</select>
		</div>

		<div class="mb-10">
			<label class="required form-label">Beschreibung</label>
			<textarea name="text" class="tinymce form-control" rows="12" required></textarea>
		</div>

		<div class="text-end">
			<span id="saveTicket" class="btn btn-primary">
				<i class="fas fa-save me-2"></i> Ticket erstellen
			</span>
		</div>
	</form>
</div>
