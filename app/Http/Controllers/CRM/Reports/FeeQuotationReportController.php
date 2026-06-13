<?php

namespace App\Http\Controllers\CRM\Reports;

use App\Http\Controllers\Controller;
use App\Models\CRM\CrmCourse;
use App\Models\CRM\CrmFeeQuotation;
use App\Models\CRM\CrmFollowup;
use App\Models\CRM\CrmLead;
use App\Models\CRM\CrmLeadSource;
use App\Models\CRM\CrmLeadStatus;
use App\Models\CRM\CrmStandard;
use App\Models\User;

class FeeQuotationReportController extends Controller
{
    public function index()
    {
        $quotations = CrmFeeQuotation::with(['lead', 'course', 'creator'])->latest()->paginate(30);
        return view('crm.reports.fee-quotation.index', compact('quotations'));
    }
}
