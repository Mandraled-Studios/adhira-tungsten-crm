<x-mail::message>
Dear {{$client->client_name}},

We hope this email finds you well.

We are writing to you today to share your invoice ({{$invoice->invoice_number}}) for 
{{$invoice->task->taskType->name}} services we recently completed for you. 
You'll find the invoice attached to this email for your review.

The total amount due for this invoice is ₹{{$invoice->total}}. 

<section class="ms-invoice">
  <header class="">
    <div id="logo" class="text-center">
      <img height="100" src="{{asset($emailDetails['logo'])}}">
    </div>
    @php
        $phone = "N/A";
        if($invoice->task->client->mobile != NULL || $invoice->task->client->mobile != "N/A") {
            $phone = $invoice->task->client->mobile;
        } else {
            if($invoice->task->client->whatsapp != NULL || $invoice->task->client->whatsapp != "N/A" ) {
                $phone = $invoice->task->client->whatsapp;
            }
        }
    @endphp
    <div class="ms-px-3">
        <div id="company" class="">
        <div> {{ $invoice->task->billing_company }} </div>
        <div style="max-width: 260px; float:right; white-space: pre-line;"> {{ $emailDetails['address'] }} </div>
        <div style="clear: both;"> <a href="tel:{{$emailDetails['phone']}}">  {{$emailDetails['phone']}} </a> </div>
        <div><a href="mailto:{{$emailDetails['email']}}"> {{$emailDetails['email']}} </a></div>
        </div>  
        <div id="project">
        <div><span>To</span> {{ $invoice->task->client->company_name }} </div>
        <div><span>C/o</span> Mr./Ms. {{ $invoice->task->client->client_name??"N/A" }} </div>
        <div><span>ADDRESS</span> {{ $invoice->task->client->address??"N/A" }}, {{$invoice->task->client->city}} </div>
        <div><span>PHONE</span> <a href="tel:{{$phone}}"> {{$phone}} </a></div>
        <div><span>INVOICE DATE</span> {{date('d-m-Y', strtotime($invoice->invoice_date))}} </div>
        <div><span>DUE DATE</span> {{date('d-m-Y', strtotime($invoice->duedate))}} </div>
        </div>
    </div>
  </header>
  <main class="ms-invoice-table">
    <h1 class="invoice-number"> Invoice Number: {{$invoice->invoice_number}} </h1>
    <table>
      <thead>
        <tr>
          <th class="service">SERVICE</th>
          <th>PRICE</th>
          <th>{{$invoice->tax1_label}}</th>
          @isset($invoice->tax2_label) <th> {{$invoice->tax2_label}} </th> @endisset
          <th>LINE TOTAL</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="service"> {{$invoice->task->taskType->name}} </td>
          <td class="unit"> ₹{{$invoice->subtotal}} </td>
          <td class="qty"> ₹{{$invoice->tax1}} </td>
          @isset($invoice->tax2) <td class="qty"> ₹{{$invoice->tax2}}  </td> @endisset
          <td class="total"> ₹{{$invoice->total}} </td>
        </tr>
        <tr>
          <td colspan="4">SUBTOTAL</td>
          <td class="total">₹{{$invoice->subtotal}}</td>
        </tr>
        @isset($invoice->tax2)
            @if (isset($invoice->tax1))
            <tr>
                <td colspan="4"> Total Taxes </td>
                <td class="total">₹{{$invoice->tax1 + $invoice->tax2}}</td>
            </tr>
            @endif
        @else
           @if (isset($invoice->tax1))
            <tr>
                <td colspan="4"> Total Taxes </td>
                <td class="total">₹{{$invoice->tax1}}</td>
            </tr>
           @endif
        @endisset
        <tr>
          <td colspan="4" class="grand total">GRAND TOTAL</td>
          <td class="grand total">₹{{$invoice->total}}</td>
        </tr>
      </tbody>
    </table>
    <div id="notices">
      <div>NOTICE:</div>
      <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
    </div>
  </main>
  <footer>
    Invoice was created on a computer and is valid without the signature and seal.
  </footer>
</section>

Please remit payment within 5 days of receipt of this email. 
You can make payment via the QR Code below:
<img class="qr-code" src="{{asset($emailDetails['qrcode'])}}" />

If you have any questions regarding the invoice or payment, please do not hesitate to contact us.

Thank you for your continued business. We appreciate your trust in our services.

{{--
<x-mail::button :url="''">
Button Text
</x-mail::button>
--}}

Thanks & Sincerely,<br>
{{ config('app.name') }},
adhiraassociates.com
</x-mail::message>
