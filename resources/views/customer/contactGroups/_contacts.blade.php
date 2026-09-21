@if($contact->cache)
    <div class="bm-grid bm-grid--stats" style="margin-bottom: 16px;">
        <div class="bm-stat">
            <span class="bm-stat__label">{{ __('locale.labels.total') }}</span>
            <span class="bm-stat__value">{{ $contact->readCache('TotalSubscribers') }}</span>
        </div>
        <div class="bm-stat">
            <span class="bm-stat__label">{{ __('locale.contacts.active_contacts') }}</span>
            <span class="bm-stat__value bm-stat__value--ok">{{ $contact->readCache('SubscribersCount') }}</span>
        </div>
        <div class="bm-stat">
            <span class="bm-stat__label">{{ __('locale.contacts.inactive_contacts') }}</span>
            <span class="bm-stat__value bm-stat__value--bad">{{ $contact->readCache('UnsubscribesCount') }}</span>
        </div>
    </div>
@endif


<div id="datatables-basic">

    <div class="bm-row" style="margin-bottom: 16px;">
        @can('view_contact')
            <div class="btn-group">
                <button
                        class="bm-btn dropdown-toggle"
                        type="button"
                        id="bulk_actions"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    {{ __('locale.labels.actions') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="bulk_actions">
                    <a class="dropdown-item bulk-subscribe" href="#"><i
                                data-feather="check"></i> {{ __('locale.labels.subscribe') }}</a>
                    <a class="dropdown-item bulk-unsubscribe" href="#"><i
                                data-feather="stop-circle"></i> {{ __('locale.labels.unsubscribe') }}</a>
                    <a class="dropdown-item bulk-copy" href="#"><i
                                data-feather="copy"></i> {{ __('locale.buttons.copy') }}</a>
                    <a class="dropdown-item bulk-move" href="#"><i
                                data-feather="move"></i> {{ __('locale.buttons.move') }}</a>
                    @if(Auth::user()->can('delete_contact'))
                        <a class="dropdown-item bulk-delete" href="#"><i
                                    data-feather="trash"></i> {{ __('locale.datatables.bulk_delete') }}</a>
                    @endif
                </div>
            </div>
        @endcan

        @can('create_contact')
            <div class="btn-group">
                <a href="{{route('customer.contact.create', $contact->uid)}}"
                   class="bm-btn bm-btn--primary"> + {{__('locale.buttons.add_new')}}</a>
            </div>
        @endcan

        @can('view_contact')
            <div class="btn-group">
                <a href="{{ route('customer.contact.import', $contact->uid) }}"
                   class="bm-btn"> {{__('locale.buttons.import')}}</a>
            </div>

            <div class="btn-group">
                <button id="export-contact"
                        class="bm-btn"> {{__('locale.buttons.export')}}</button>
            </div>


            <div class="btn-group">
                <button
                        class="bm-btn dropdown-toggle"
                        type="button"
                        id="columns"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    {{ __('locale.labels.columns') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="columns">
                    @php
                        $key = 6;
                    @endphp
                    @foreach ($contact->getFields as  $field)
                        @if ($field->tag != "PHONE")
                            <a class="dropdown-item toggle-vis" href="#" data-column="{{$key++}}"><i class="toggle-icon"
                                                                                                     data-feather="eye"></i> {{ $field->label }}
                            </a>
                        @endif
                    @endforeach

                </div>
            </div>
        @endcan


    </div>

    <div>
    </div>

    <div class="bm-card bm-card--flush bm-dt">
        <div>
            <div>
                <table class="table datatables-basic">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ __('locale.labels.id') }}</th>
                        <th>{{__('locale.menu.Contacts')}}</th>
                        <th>{{__('locale.labels.updated_at')}}</th>
                        <th>{{__('locale.labels.status')}}</th>
                        @foreach ($contact->getFields as $key => $field)
                            @if ($field->tag != "PHONE")
                                <th>{{ $field->label }}</th>
                            @endif
                        @endforeach
                        <th>{{__('locale.labels.actions')}}</th>

                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="exportContactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="{{route('customer.contact.export', $contact->uid)}}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Overview</h5>
                </div>
                <div class="modal-body">

                    @csrf
                    <div class="col-12">
                        <div class="mb-1">
                            <button type="button" id="select-all-btn" class="btn btn-primary btn-sm">
                                <i data-feather="check-square"></i>&nbsp;{{__('locale.labels.select_all')}}
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label for="contact_fields"
                                   class="form-label required">{{ __('locale.contacts.contact_fields') }}</label>
                            <select class="select2 form-select" name="contact_fields[]" multiple="multiple"
                                    id="contact_fields" required>
                                @foreach ($fields as $field)
                                    <option value="{{ $field->tag }}"> {{ucwords($field->label)}}</option>
                                @endforeach
                            </select>

                            @error('contact_fields')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" id="include_phone" name="include_phone" class="form-check-input"
                                       value="true" checked>
                                <label class="form-check-label" for="include_phone">
                                    {{ __('locale.contacts.force_export_phone_number') }}
                                </label> &nbsp;

                                <span role="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                      title="{{ __('locale.contacts.force_export_phone_number_help') }}"
                                      data-tippy-id="customer.contacts.show.force_export_phone_number"><i
                                            data-feather="info"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="closeExportContact" type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                data-feather="x"></i> {{ __('locale.buttons.close') }}</button>
                    <button type="submit" class="btn btn-primary"
                            id="finalExportContact">{{ __('locale.buttons.export') }} <i data-feather="download"></i>
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
