@component('mail::message')
# MDRRMO Two-Factor Authentication Code

Hello **{{ $user_name }}**,

Your MDRRMO system verification code is:

@component('mail::panel')
**{{ str_pad($two_factor_code, 6, '0', STR_PAD_LEFT) }}**
@endcomponent

**Important Details:**
- This code will expire in **10 minutes**
- Municipality: {{ $municipality }}
- Requested at: {{ now()->format('M d, Y h:i A') }}

If you did not request this code, please contact your MDRRMO administrator immediately.

**Security Notice:** Never share this code with anyone. MDRRMO staff will never ask for your verification code.

---
**Municipal Disaster Risk Reduction and Management Office**
Maramag, Bukidnon

@component('mail::button', ['url' => route('login')])
Back to MDRRMO Login
@endcomponent

Thanks,<br>
MDRRMO Maramag System
@endcomponent
