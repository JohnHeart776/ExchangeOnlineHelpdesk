<a href="/admin/notificationtemplates/" class="btn btn-sm btn-light-info mb-5">
	<i class="fas fa-arrow-left"></i> Back
</a>

<!--begin::Properties Widget-->
<div class="card mb-5 mb-xl-8">
	<!--begin::Header-->
	<div class="card-header border-0 pt-5">

		<h3 class="card-title align-items-start flex-column">
			<span class="card-label fw-bold fs-3 mb-1">Notification template
				<a href="#" class="editable text-hover-primary text-gray-800 text-decoration-none"
				   data-type="text"
				   data-pk="{$template->getGuid()}"
				   data-name="Name"
				   data-url="/api/admin/notificationtemplate/update.json">{$template->getName()}</a>
			</span>
			<span class="text-muted mt-1 fw-semibold fs-7">Details of the notification template</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-3">
		<!--begin::Table container-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
				<!--begin::Table body-->
				<tbody>

				<tr>
					<td>
						<div class="d-flex align-items-center">
							<div class="d-flex justify-content-start flex-column w-100">
								<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3 mb-4">
									<tbody>
									<tr>
										<td class="fw-bold" style="width: 200px">Name:</td>
										<td>
											<a href="#" class="editable text-gray-900 text-hover-primary fs-6" id="template-name"
											   data-type="text"
											   data-pk="{$template->getGuid()}"
											   data-name="Name"
											   data-url="/api/admin/notificationtemplate/update.json"
											   data-value="{$template->getName()}"
											></a>
										</td>
									</tr>
									<tr>
										<td class="fw-bold">Aktiviert:</td>
										<td>
											<a href="#" class="editable text-gray-900 text-hover-primary fs-6" id="template-enabled"
											   data-type="select"
											   data-pk="{$template->getGuid()}"
											   data-name="Enabled"
											   data-url="/api/admin/notificationtemplate/update.json"
											   data-value="{$template->getEnabled()}"
											   data-source="[{ value: 1, text: 'Yes' }, { value: 0, text: 'No' }]"
											></a>
										</td>
									</tr>
									<tr>
										<td class="fw-bold">E-Mail Betreff:</td>
										<td>
											<a href="#" class="editable text-gray-900 text-hover-primary fs-6" id="template-mailsubject"
											   data-type="text"
											   data-pk="{$template->getGuid()}"
											   data-name="MailSubject"
											   data-url="/api/admin/notificationtemplate/update.json"
											   data-value="{$template->getMailSubject()}"
											></a>
										</td>
									</tr>
									</tbody>
								</table>
								<h3 class="card-title align-items-start flex-column mb-4">
									<span class="card-label fw-bold fs-3 mb-1">Inhalt</span>
								</h3>
								<div class="mb-4 w-100">
									<textarea id="templatetext-content" class="tinymce w-100" style="height: 800px;">{$template->getMailTextForTinyMce()}</textarea>

									<input type="hidden" id="templatetext-guid" value="{$template->getGuid()}"/>
									<input type="hidden" id="addToGeneratePrompt" value="{config::get("ai.prompt.suffix.notificationtemplate.generate")}"/>

									<div class="d-flex justify-content-end mt-2">

										<button type="button" class="btn btn-light-info ms-2 me-3" id="generateAiText">
											<span class="indicator-label"><i class="fas fa-gear-code"></i> Text generieren</span>
										</button>

										<button type="button" class="btn btn-primary" id="save-template-content">
											<span class="indicator-label"><i class="fas fa-save"></i> Änderungen speichern</span>
										</button>

									</div>

								</div>

								<h3 class="card-title align-items-start flex-column mb-4 mt-4">
									<span class="card-label fw-bold fs-3 mb-1">Verfügbare Platzhalter</span>
								</h3>

								<div class="table-responsive">
									<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
										<thead>
										<tr class="fw-bold text-muted">
											<th>Platzhalter</th>
											<th>Beschreibung</th>
										</tr>
										</thead>
										<tbody>
										<tr>
											<td><code class="addToEditor">{literal}{{dateTime}}{/literal}</code></td>
											<td>Aktuelles Datum und Uhrzeit (Format: DD.MM, HH:MM)</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{date}}{/literal}</code></td>
											<td>Aktuelles Datum (Format: DD.MM.YYYY)</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{time}}{/literal}</code></td>
											<td>Aktuelle Uhrzeit (Format: HH:MM)</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{givenName}}{/literal}</code> oder <code class="addToEditor">{literal}{{name}}{/literal}</code></td>
											<td>Vorname des Benutzers</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{surname}}{/literal}</code></td>
											<td>Nachname des Benutzers</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{fullName}}{/literal}</code> oder <code class="addToEditor">{literal}{{displayName}}{/literal}</code></td>
											<td>Vollständiger Name des Benutzers</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketNumber}}{/literal}</code></td>
											<td>Ticketnummer</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketMailSubject}}{/literal}</code></td>
											<td>E-Mail-Betreff des Tickets</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketSubject}}{/literal}</code></td>
											<td>Betreff des Tickets</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketMarker}}{/literal}</code></td>
											<td>Ticket-Marker für E-Mail-Betreff</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketLink}}{/literal}</code></td>
											<td>Link zum Ticket</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketLinkAbsoluteInA}}{/literal}</code></td>
											<td>HTML-Link zum Ticket (als &lt;a&gt;-Element)</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{ticketLinkAbsolute}}{/literal}</code></td>
											<td>Absoluter Link zum Ticket</td>
										</tr>
										<tr>
											<td><code class="addToEditor">{literal}{{finish}}{/literal}</code></td>
											<td>Standard-Grußformel</td>
										</tr>
										</tbody>
									</table>
								</div>

							</div>
						</div>
					</td>
				</tr>

				</tbody>
				<!--end::Table body-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table container-->
	</div>
	<!--begin::Body-->
</div>
<!--end::Properties Widget-->
