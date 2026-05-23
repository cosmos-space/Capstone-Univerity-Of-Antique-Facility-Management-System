# TODO - Facilities form booking/PDF/signatory updates

- [ ] Update FacilitiesFormPdfController::generate to allow status `approved` OR `converted` (so Set Booking no longer breaks PDF generation)
- [ ] Update FacilitiesFormPdfController signature/date filling to use payload:
  - noted_datetime (requester auto)
  - approved_head_name + approved_datetime (GSU approval)
- [ ] Update College/FormController@storeFacilities and Org/FormController@storeFacilities to store `noted_datetime` in payload (auto)
- [ ] Add Head of Office (GSU) selection UI in resources/views/admin/forms/facilities_show.blade.php when status is pending
- [ ] Update Admin/FormSubmissionController@approve to accept selected `gsu_head_id`, store approved_head_name + approved_datetime in payload, then set status to `approved`
- [ ] Update status display mapping anywhere it prints `ucfirst($submission->status)`:
  - show `converted` as `Booked`
- [ ] Keep “Generate PDF” button available after booking (likely already via route visibility; verify)
- [ ] Run basic PHP syntax check (php -l) on modified files

