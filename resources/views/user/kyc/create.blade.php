@extends('layouts.master')

@section('hide_page_header', true)
@section('title', 'Become an Affiliate')

@section('contents')
    @php
        $refererId = old('referer_id', $kyc?->referer_id);
        $affiliateId = old('affiliate_id', $kyc?->affiliate_id);
        $documentType = old('doc_type', $kyc?->doc_type);
        $documentNumber = old('document_number', $kyc?->document_number);
    @endphp

    <div class="kyc-page">
        <div class="kyc-container">
            <div class="kyc-intro">
                <div class="kyc-intro__content">
                    <span class="kyc-eyebrow">Right platform</span>
                    <h1>Become an affiliate</h1>
                    <p>Complete your identity details to start building your network and unlock affiliate benefits.</p>
                    <div class="kyc-intro__points">
                        <span><i class="fas fa-shield-alt"></i> Secure verification</span>
                        <span><i class="fas fa-bolt"></i> Quick one-step application</span>
                    </div>
                </div>
                <div class="kyc-intro__orb" aria-hidden="true"></div>
            </div>

            @if (session('success'))
                <section class="kyc-card kyc-success-card" aria-labelledby="kyc-success-title">
                    <div class="kyc-success-card__icon"><i class="fas fa-check"></i></div>
                    <span class="kyc-eyebrow">Application received</span>
                    <h2 id="kyc-success-title">Thank you for submitting the form</h2>
                    <p>Thank you for submitting your form for the affiliate program. Our team will review your application and contact you with the next steps.</p>
                    <a href="{{ route('dashboard') }}" class="kyc-submit"><span>Go to dashboard</span> <i class="fas fa-arrow-right"></i></a>
                </section>
            @else
            <section class="kyc-card" aria-labelledby="kyc-form-title">
                <div class="kyc-card__header">
                    <div>
                        <span class="kyc-eyebrow">Affiliate application</span>
                        <h2 id="kyc-form-title">Personal information</h2>
                        <p>Provide the details below to submit your application.</p>
                    </div>
                    <span class="kyc-card__badge"><i class="fas fa-check-circle"></i> One step</span>
                </div>

                <form id="kycForm" action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data" class="kyc-form">
                    @csrf
                    <div class="kyc-form__grid">
                        <div class="kyc-field">
                            <label for="reference_user">Reference ID <span class="kyc-required">*</span></label>
                            <select class="form-control select2 reference_user" name="referer_id" id="reference_user" required data-placeholder="Select a reference">
                                <option value=""></option>
                                @foreach ($affiliates as $affiliate)
                                    <option value="{{ $affiliate->id }}" @selected($refererId == $affiliate->id)>
                                        {{ $affiliate?->kyc?->affiliate_id }} - {{ $affiliate->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="kyc-help">Choose the affiliate who referred you.</small>
                        </div>

                        <div class="kyc-field">
                            <label for="affiliate_id">Affiliate ID</label>
                            <div class="kyc-input-wrap">
                                <i class="fas fa-id-card" aria-hidden="true"></i>
                                <input type="text" class="form-control affiliate_id" name="affiliate_id" id="affiliate_id"
                                    value="{{ $affiliateId }}" placeholder="Generated after selecting a reference" readonly>
                            </div>
                            <small class="kyc-help">This ID is linked automatically to your reference.</small>
                        </div>

                        <div class="kyc-field kyc-field--full">
                            <label>Identity document <span class="kyc-required">*</span></label>
                            <div class="kyc-document-options" role="radiogroup" aria-label="Identity document type">
                                <label class="kyc-radio-card" for="nid">
                                    <input type="radio" id="nid" name="doc_type" value="{{ App\Models\Kyc::NID }}" @checked($documentType == App\Models\Kyc::NID) required>
                                    <span class="kyc-radio-card__icon"><i class="fas fa-address-card"></i></span>
                                    <span><strong>NID</strong><small>National ID</small></span>
                                    <i class="fas fa-check-circle kyc-radio-card__check"></i>
                                </label>
                                <label class="kyc-radio-card" for="bc">
                                    <input type="radio" id="bc" name="doc_type" value="{{ App\Models\Kyc::BC }}" @checked($documentType == App\Models\Kyc::BC)>
                                    <span class="kyc-radio-card__icon"><i class="fas fa-file-alt"></i></span>
                                    <span><strong>Birth Certificate</strong><small>Birth registration</small></span>
                                    <i class="fas fa-check-circle kyc-radio-card__check"></i>
                                </label>
                                <label class="kyc-radio-card" for="passport">
                                    <input type="radio" id="passport" name="doc_type" value="{{ App\Models\Kyc::PASSPORT }}" @checked($documentType == App\Models\Kyc::PASSPORT)>
                                    <span class="kyc-radio-card__icon"><i class="fas fa-passport"></i></span>
                                    <span><strong>Passport</strong><small>International passport</small></span>
                                    <i class="fas fa-check-circle kyc-radio-card__check"></i>
                                </label>
                            </div>
                        </div>

                        <div class="kyc-field kyc-field--full">
                            <label for="document_number">Document number <span class="kyc-required">*</span></label>
                            <div class="kyc-input-wrap">
                                <i class="fas fa-hashtag" aria-hidden="true"></i>
                                <input type="text" class="form-control" id="document_number" name="document_number"
                                    value="{{ $documentNumber }}" placeholder="Enter your document number" required>
                            </div>
                        </div>
                    </div>

                    <div class="kyc-form__footer">
                        <p><i class="fas fa-lock"></i> Your information is handled securely and reviewed by our team.</p>
                        <button type="submit" class="kyc-submit"><span>Submit application</span> <i class="fas fa-arrow-right"></i></button>
                    </div>
                </form>
            </section>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('.select2').each(function() {
                $(this).select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder') || 'Please select',
                    allowClear: true
                });
            });

            $('.reference_user').on('change', function() {
                const referenceUserId = $(this).val();

                if (!referenceUserId) {
                    $('.affiliate_id').val('');
                    return;
                }

                $('.affiliate_id').val('Loading...');
                $.get("{{ route('load_affiliate_id') }}", { ref_user_id: referenceUserId })
                    .done(function(data) {
                        $('.affiliate_id').val(data.reference_id || '');
                    })
                    .fail(function() {
                        $('.affiliate_id').val('');
                        toastr.error('Unable to load the affiliate ID. Please try again.');
                    });
            });

            $('#kycForm').on('submit', function(event) {
                const form = this;

                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.classList.add('was-validated');
                }
            });
        });
    </script>

    <style>
        .kyc-page { min-height: calc(100vh - 74px); width: 100%; padding: 34px 24px 56px; background: #f4f7fb; }
        .kyc-container { width: 100%; max-width: none; margin: 0; }
        .kyc-intro { position: relative; overflow: hidden; min-height: 190px; padding: 27px 46px; color: #fff; border-radius: 24px 24px 0 0; background: linear-gradient(116deg, #11243e 0%, #183653 60%, #20577a 100%); box-shadow: 0 16px 32px rgba(16, 39, 66, .12); }
        .kyc-intro__content { position: relative; z-index: 1; max-width: 650px; }
        .kyc-eyebrow { display: block; color: #f2464e; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .kyc-intro h1 { margin: 8px 0 5px; font-size: clamp(30px, 4vw, 42px); font-weight: 700; letter-spacing: -.04em; }
        .kyc-intro p { max-width: 580px; margin: 0; color: #b9cce0; font-size: 16px; line-height: 1.65; }
        .kyc-intro__points { display: flex; flex-wrap: wrap; gap: 22px; margin-top: 16px; color: #e5eef7; font-size: 13px; font-weight: 600; }
        .kyc-intro__points i { margin-right: 7px; color: #ff5962; }
        .kyc-intro__orb { position: absolute; right: 5%; top: -150px; width: 380px; height: 380px; border: 1px solid rgba(255,255,255,.14); border-radius: 50%; box-shadow: 0 0 0 32px rgba(255,255,255,.025), 0 0 0 76px rgba(255,255,255,.025); background: radial-gradient(circle, rgba(224,47,83,.28), transparent 63%); }
        .kyc-card { margin: 0 auto; padding: 36px 46px 40px; border-radius: 0 0 24px 24px; background: #fff; box-shadow: 0 18px 36px rgba(16, 39, 66, .1); }
        .kyc-success-card { min-height: 360px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .kyc-success-card__icon { display: inline-flex; width: 72px; height: 72px; align-items: center; justify-content: center; margin-bottom: 20px; color: #fff; border-radius: 50%; background: #25a568; box-shadow: 0 0 0 10px #e8f8ef; font-size: 30px; }
        .kyc-success-card h2 { max-width: 620px; margin: 12px 0 10px; color: #11243e; font-size: clamp(27px, 4vw, 38px); letter-spacing: -.04em; }
        .kyc-success-card p { max-width: 620px; margin: 0 0 26px; color: #71839a; font-size: 16px; line-height: 1.7; }
        .kyc-card__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding-bottom: 25px; border-bottom: 1px solid #e6edf4; }
        .kyc-card__header h2 { margin: 7px 0 5px; color: #11243e; font-size: 28px; letter-spacing: -.03em; }
        .kyc-card__header p { margin: 0; color: #71839a; font-size: 14px; }
        .kyc-card__badge { flex: 0 0 auto; padding: 9px 13px; color: #148a55; border: 1px solid #bfe9d1; border-radius: 999px; background: #effbf4; font-size: 12px; font-weight: 700; }
        .kyc-card__badge i { margin-right: 5px; }
        .kyc-form { padding-top: 28px; }
        .kyc-form__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px 26px; }
        .kyc-field--full { grid-column: 1 / -1; }
        .kyc-field label { display: block; margin-bottom: 9px; color: #223853; font-size: 14px; font-weight: 700; }
        .kyc-required { color: #e02f3d; }
        .kyc-help { display: block; margin-top: 7px; color: #8797aa; font-size: 12px; }
        .kyc-input-wrap { position: relative; }
        .kyc-input-wrap > i { position: absolute; top: 50%; left: 16px; z-index: 1; color: #7890a7; transform: translateY(-50%); }
        .kyc-input-wrap .form-control { padding-left: 44px; }
        .kyc-form .form-control { min-height: 48px; border: 1px solid #dce6f0; border-radius: 11px; color: #18314e; background: #fbfdff; box-shadow: none; transition: .2s ease; }
        .kyc-form .form-control:focus { border-color: #ee3945; background: #fff; box-shadow: 0 0 0 4px rgba(238,57,69,.1); }
        .kyc-form .select2-container { width: 100% !important; }
        .kyc-form .select2-container--default .select2-selection--single { height: 48px; padding: 9px 14px; border: 1px solid #dce6f0; border-radius: 11px; background: #fbfdff; }
        .kyc-form .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 0; color: #18314e; line-height: 28px; }
        .kyc-form .select2-container--default .select2-selection--single .select2-selection__arrow { top: 10px; right: 11px; }
        .kyc-form .select2-container--default.select2-container--open .select2-selection--single { border-color: #ee3945; box-shadow: 0 0 0 4px rgba(238,57,69,.1); }
        .kyc-document-options { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .kyc-radio-card { position: relative; display: flex; align-items: center; gap: 11px; min-height: 72px; margin: 0; padding: 12px 14px; color: #223853; border: 1px solid #dce6f0; border-radius: 13px; background: #fbfdff; cursor: pointer; transition: .2s ease; }
        .kyc-radio-card:hover, .kyc-radio-card:has(input:checked) { border-color: #ee3945; background: #fff6f7; box-shadow: 0 5px 14px rgba(238,57,69,.1); }
        .kyc-radio-card input { position: absolute; opacity: 0; }
        .kyc-radio-card__icon { display: inline-flex; flex: 0 0 35px; width: 35px; height: 35px; align-items: center; justify-content: center; color: #dc303c; border-radius: 9px; background: #ffe8ea; }
        .kyc-radio-card strong, .kyc-radio-card small { display: block; }
        .kyc-radio-card strong { font-size: 13px; }
        .kyc-radio-card small { margin-top: 2px; color: #8797aa; font-size: 11px; font-weight: 400; }
        .kyc-radio-card__check { margin-left: auto; color: #e2313e; opacity: 0; }
        .kyc-radio-card:has(input:checked) .kyc-radio-card__check { opacity: 1; }
        .kyc-form__footer { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e6edf4; }
        .kyc-form__footer p { margin: 0; color: #7d8fa4; font-size: 12px; }
        .kyc-form__footer p i { margin-right: 6px; color: #35a36c; }
        .kyc-submit { display: inline-flex; align-items: center; justify-content: center; gap: 15px; min-width: 215px; min-height: 50px; padding: 12px 22px; color: #fff; border: 0; border-radius: 11px; background: #182941; box-shadow: 0 10px 20px rgba(24,41,65,.18); font-size: 14px; font-weight: 700; cursor: pointer; transition: .2s ease; }
        .kyc-submit:hover { background: #e2313e; box-shadow: 0 10px 20px rgba(226,49,62,.2); transform: translateY(-1px); }
        @media (max-width: 768px) { .kyc-page { padding: 18px 12px 34px; } .kyc-intro { min-height: auto; padding: 30px 25px; border-radius: 18px 18px 0 0; } .kyc-intro__orb { right: -150px; } .kyc-card { padding: 27px 20px 28px; border-radius: 0 0 18px 18px; } .kyc-card__header, .kyc-form__footer { display: block; } .kyc-card__badge { display: inline-block; margin-top: 18px; } .kyc-form__grid, .kyc-document-options { grid-template-columns: 1fr; } .kyc-field--full { grid-column: auto; } .kyc-submit { width: 100%; margin-top: 18px; } }
    </style>
@endsection
