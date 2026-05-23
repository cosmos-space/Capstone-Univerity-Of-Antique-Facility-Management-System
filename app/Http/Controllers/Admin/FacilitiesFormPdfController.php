<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ConvertsToPdf;
use App\Models\Facility;
use App\Models\FormSubmission;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FacilitiesFormPdfController extends Controller
{
    use ConvertsToPdf;

    public function generate(FormSubmission $submission): BinaryFileResponse
    {
        if (!$submission->isFacilitiesUtilization() || !$submission->isApproved()) {
            abort(404);
        }

        $payload = $submission->payload ?? [];
        $facility = null;

        if (!empty($payload['facility_id'])) {
            $facility = Facility::find($payload['facility_id']);
        }

        $controlNo      = $payload['control_no']    ?? '';
        $dateRequest    = $payload['date_request']  ?? now()->toDateString();
        $requesterName  = $payload['requester_name'] ?? ($submission->requester->name ?? '');
        $dateActivity   = $payload['date_activity'] ?? '';
        $timeRange      = $payload['time_range'] ?? [];
        $timeActivity   = '';
        if (!empty($timeRange['start']) && !empty($timeRange['end'])) {
            $timeActivity = $timeRange['start'].' - '.$timeRange['end'];
        }
        $purpose        = $payload['purpose'] ?? '';
        $equipment = $payload['equipment'] ?? [];

        $facilityToKeyMap = [
            'BUSALAN HALL' => 'busalan_hall',
            'AVR-USA HALL' => 'paghiusa_hall',
            'E-HUB'        => 'ehub',
            'BALAY NI JUAN'=> 'balay_ni_juan',
            'ICT AVR'      => 'ict_avr',
            'CEA AVR'      => 'cea_avr',
            'CBA AVR'      => 'cba_avr',
            'NEW AVR'      => 'new_avr',
            'GRAND STAND'  => 'grandstand',
            'COVERED GYM'  => 'covered_gym',
            'TRACK OVAL'   => 'track_oval',
        ];

        $checked = "\u{2714}";
        $unchecked = '';

        $venueCheckboxes = [
            'busalan_hall'  => $unchecked,
            'paghiusa_hall'  => $unchecked,
            'ehub'           => $unchecked,
            'balay_ni_juan' => $unchecked,
            'ict_avr'       => $unchecked,
            'cea_avr'       => $unchecked,
            'cba_avr'       => $unchecked,
            'new_avr'       => $unchecked,
            'grandstand'    => $unchecked,
            'covered_gym'   => $unchecked,
            'track_oval'    => $unchecked,
            'others'        => $unchecked,
        ];

        $venueOthersText = $payload['venue_others'] ?? '';

        if ($facility) {
            $facilityName = strtoupper(trim($facility->name));
            if (array_key_exists($facilityName, $facilityToKeyMap)) {
                $venueCheckboxes[$facilityToKeyMap[$facilityName]] = $checked;
            } else {
                $venueCheckboxes['others'] = $checked;
                $venueOthersText = $venueOthersText ?: $facility->name;
            }
        }

        if (!empty($payload['venue_others'])) {
            $venueCheckboxes['others'] = $checked;
            $venueOthersText = $payload['venue_others'];
        }

        $templatePath = base_path('app/templates/FACILITIES-AND-UTILIZATION-FORM-TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/templates/FACILITIES-AND-UTILIZATION-FORM-TEMPLATE.docx');
        }

        if (!file_exists($templatePath)) {
            abort(500, 'Facilities Utilization Form template not found.');
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('control_no',       $controlNo);
        $template->setValue('date_request',     $dateRequest);
        $template->setValue('requester_name',   $requesterName);
        $template->setValue('requester_contact',$payload['requester_contact'] ?? '');
        $template->setValue('date_activity',    $dateActivity);
        $template->setValue('time_activity',    $timeActivity);
        $template->setValue('purpose',          $purpose);

        $template->setValue('busalian_hall', $venueCheckboxes['busalian_hall'] ?? '');
        $template->setValue('paghiusa_hall', $venueCheckboxes['paghiusa_hall'] ?? '');
        $template->setValue('ehub',          $venueCheckboxes['ehub'] ?? '');
        $template->setValue('balay_ni_juan', $venueCheckboxes['balay_ni_juan'] ?? '');
        $template->setValue('ict_avr',       $venueCheckboxes['ict_avr'] ?? '');
        $template->setValue('cea_avr',       $venueCheckboxes['cea_avr'] ?? '');
        $template->setValue('cba_avr',       $venueCheckboxes['cba_avr'] ?? '');
        $template->setValue('new_avr',       $venueCheckboxes['new_avr'] ?? '');
        $template->setValue('grandstand',    $venueCheckboxes['grandstand'] ?? '');
        $template->setValue('covered_gym',   $venueCheckboxes['covered_gym'] ?? '');
        $template->setValue('track_oval',    $venueCheckboxes['track_oval'] ?? '');
        $template->setValue('others',        $venueCheckboxes['others'] ?? '');
        $template->setValue('venue_others',  $venueOthersText);

        $template->setValue('qty_monobloc', $equipment['monobloc_chair'] ?? '');
        $template->setValue('qty_table',    $equipment['table'] ?? '');
        $template->setValue('qty_fan',      $equipment['electric_fan'] ?? '');
        $template->setValue('qty_rostrum',  $equipment['rostrum'] ?? '');
        $template->setValue('qty_flag',     $equipment['flag'] ?? '');
        $template->setValue('qty_sound',    $equipment['sound'] ?? '');
        $template->setValue('qty_led',      $equipment['led'] ?? '');

        $template->setValue('req_signature',      '');
        $template->setValue('req_name',           $requesterName);
        $template->setValue('req_datetime',       now()->format('F d, Y  h:i A'));
        $template->setValue('noted_signature',    '');
        $template->setValue('noted_name',         '');
        $template->setValue('noted_datetime',     '');
        $template->setValue('approved_signature', '');
        $template->setValue('approved_name',      '');
        $template->setValue('approved_datetime',  '');

        $docxPath = tempnam(sys_get_temp_dir(), 'facilities_') . '.docx';
        $template->saveAs($docxPath);

        return $this->convertAndDownload($docxPath, 'Facilities-Utilization-Form-'.$submission->id);
    }
}
