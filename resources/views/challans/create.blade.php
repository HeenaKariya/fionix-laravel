@extends('adminlte::page')

@section('title', 'Add Bill/ Challan')

@section('content_header')
    <h1>Add Bill/Challan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('projects.challans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="project_id">Project Site Name</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('project_id'))
                        <span class="text-danger">{{ $errors->first('project_id') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="bill_date">Bill/Challan Date</label>
                    <input type="date" name="bill_date" class="form-control" value="{{ old('bill_date', date('Y-m-d')) }}" required>
                    @if ($errors->has('bill_date'))
                        <span class="text-danger">{{ $errors->first('bill_date') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required>
                    @if ($errors->has('amount'))
                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="payment_type">Payment Type</label>
                    <select name="payment_type" id="payment_type" class="form-control" required>
                        <option value="Bank Transfer" {{ old('payment_type') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Cash" {{ old('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Cheque" {{ old('payment_type') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="UPI" {{ old('payment_type') == 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="DD(Demand Draft)" {{ old('payment_type') == 'DD(Demand Draft)' ? 'selected' : '' }}>DD(Demand Draft)</option>
                    </select>
                    @if ($errors->has('payment_type'))
                        <span class="text-danger">{{ $errors->first('payment_type') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="expense_category">Expense Category</label>
                    <select name="expense_category" class="form-control" required>
                        <option value="CANTEEN EXP" {{ old('expense_category') == 'CANTEEN EXP' ? 'selected' : '' }}>CANTEEN EXP.</option>
                        <option value="COMPUTER REPARING EXP" {{ old('expense_category') == 'COMPUTER REPARING EXP' ? 'selected' : '' }}>COMPUTER REPARING EXP.</option>
                        <option value="FESTIVAL EXP" {{ old('expense_category') == 'FESTIVAL EXP' ? 'selected' : '' }}>FESTIVAL EXP.</option>
                        <option value="MOBILE & TELEHPONE RECHARGE EXP" {{ old('expense_category') == 'MOBILE & TELEHPONE RECHARGE EXP' ? 'selected' : '' }}>MOBILE & TELEHPONE RECHARGE EXP.</option>
                        <option value="OFFICE EXP" {{ old('expense_category') == 'OFFICE EXP' ? 'selected' : '' }}>OFFICE EXP.</option>
                        <option value="SITE MISC. ITEM PURCAHSE EXP" {{ old('expense_category') == 'SITE MISC. ITEM PURCAHSE EXP' ? 'selected' : '' }}>SITE MISC. ITEM PURCAHSE EXP.</option>
                        <option value="POSTAGE & COURIER EXP" {{ old('expense_category') == 'POSTAGE & COURIER EXP' ? 'selected' : '' }}>POSTAGE & COURIER EXP.</option>
                        <option value="PRINTING & STATIONERY EXP" {{ old('expense_category') == 'PRINTING & STATIONERY EXP' ? 'selected' : '' }}>PRINTING & STATIONERY EXP.</option>
                        <option value="SALARY EXP" {{ old('expense_category') == 'SALARY EXP' ? 'selected' : '' }}>SALARY EXP.</option>
                        <option value="TRAVELLING EXP" {{ old('expense_category') == 'TRAVELLING EXP' ? 'selected' : '' }}>TRAVELLING EXP.</option>
                        <option value="VEHICAL REPARING & MAINTANANCE EXP" {{ old('expense_category') == 'VEHICAL REPARING & MAINTANANCE EXP' ? 'selected' : '' }}>VEHICAL REPARING & MAINTANANCE EXP.</option>
                        <option value="CAR RENT EXP" {{ old('expense_category') == 'CAR RENT EXP' ? 'selected' : '' }}>CAR RENT EXP.</option>
                        <option value="DIESEL EXP" {{ old('expense_category') == 'DIESEL EXP' ? 'selected' : '' }}>DIESEL EXP.</option>
                        <option value="PETROL EXP" {{ old('expense_category') == 'PETROL EXP' ? 'selected' : '' }}>PETROL EXP.</option>
                        <option value="ELECTRIC BILL EXP" {{ old('expense_category') == 'ELECTRIC BILL EXP' ? 'selected' : '' }}>ELECTRIC BILL EXP.</option>
                        <option value="TRANSPORTING EXP" {{ old('expense_category') == 'TRANSPORTING EXP' ? 'selected' : '' }}>TRANSPORTING EXP.</option>
                        <option value="TENDER FEES EXP" {{ old('expense_category') == 'TENDER FEES EXP' ? 'selected' : '' }}>TENDER FEES EXP.</option>
                        <option value="BANK CHARGES" {{ old('expense_category') == 'BANK CHARGES' ? 'selected' : '' }}>BANK CHARGES</option>
                        <option value="RIGHT OF WAY EXP" {{ old('expense_category') == 'RIGHT OF WAY EXP' ? 'selected' : '' }}>RIGHT OF WAY EXP</option>
                        <option value="LABOUR WORK EXP" {{ old('expense_category') == 'LABOUR WORK EXP' ? 'selected' : '' }}>LABOUR WORK EXP</option>
                        <option value="HYDRO RENT EXP" {{ old('expense_category') == 'HYDRO RENT EXP' ? 'selected' : '' }}>HYDRO RENT EXP</option>
                        <option value="TANKER RENT EXP" {{ old('expense_category') == 'TANKER RENT EXP' ? 'selected' : '' }}>TANKER RENT EXP</option>
                        <option value="JCB WORK EXP" {{ old('expense_category') == 'JCB WORK EXP' ? 'selected' : '' }}>JCB WORK EXP</option>
                        <option value="SITE SURVEY EXP" {{ old('expense_category') == 'SITE SURVEY EXP' ? 'selected' : '' }}>SITE SURVEY EXP</option>
                        <option value="WATER TANKER EXP" {{ old('expense_category') == 'WATER TANKER EXP' ? 'selected' : '' }}>WATER TANKER EXP</option>
                        <option value="WAY BRIDGE EXP" {{ old('expense_category') == 'WAY BRIDGE EXP' ? 'selected' : '' }}>WAY BRIDGE EXP</option>
                        <option value="FAST TAG EXP" {{ old('expense_category') == 'FAST TAG EXP' ? 'selected' : '' }}>FAST TAG EXP</option>
                        <option value="MATERIAL SUPPLY ITEMS" {{ old('expense_category') == 'MATERIAL SUPPLY ITEMS' ? 'selected' : '' }}>MATERIAL SUPPLY ITEMS</option>
                        <option value="MACHINE & TOOLS REPAIRING EXP" {{ old('expense_category') == 'MACHINE & TOOLS REPAIRING EXP' ? 'selected' : '' }}>MACHINE & TOOLS REPAIRING EXP.</option>
                        <option value="HOTEL & ACCOMODATION EXP" {{ old('expense_category') == 'HOTEL & ACCOMODATION EXP' ? 'selected' : '' }}>HOTEL & ACCOMODATION EXP.</option>
                        <option value="FEBRICATION WORK EXP" {{ old('expense_category') == 'FEBRICATION WORK EXP' ? 'selected' : '' }}>FEBRICATION WORK EXP.</option>
                        <option value="AGRICULTURE EXP" {{ old('expense_category') == 'AGRICULTURE EXP' ? 'selected' : '' }}>AGRICULTURE EXP.</option>
                        <option value="HOUSE & HOTEL RENT EXP" {{ old('expense_category') == 'HOUSE & HOTEL RENT EXP' ? 'selected' : '' }}>HOUSE & HOTEL RENT EXP.</option>
                        <option value="MEDICAL EXP" {{ old('expense_category') == 'MEDICAL EXP' ? 'selected' : '' }}>MEDICAL EXP.</option>
                        <option value="STORE RENT" {{ old('expense_category') == 'STORE RENT' ? 'selected' : '' }}>STORE RENT.</option>
                        <option value="TRACTOR RENT" {{ old('expense_category') == 'TRACTOR RENT' ? 'selected' : '' }}>TRACTOR RENT.</option>
                        


                    </select>
                    @if ($errors->has('expense_category'))
                        <span class="text-danger">{{ $errors->first('expense_category') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="upload_image">Upload Image/Document</label>
                    <input type="file" name="upload_image[]" class="form-control" multiple required>
                    @if ($errors->has('upload_image.*'))
                        <span class="text-danger">{{ $errors->first('upload_image.*') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                    @if ($errors->has('note'))
                        <span class="text-danger">{{ $errors->first('note') }}</span>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary full-width-approve">Add Bill/Challan</button>
                @if(Auth::user()->hasRole('supervisor'))
                    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-warning full-width-cancel">Cancel</a>
                @endif
                @if (Auth::user()->hasRole('manager'))
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('manager.dashboard') }}'">Cancel</button>
                @endif
            </form>
        </div>
    </div>
@stop
