@component('mail::message')
# Xác Nhận Đặt Phòng

Xin chào {{ $booking->customer_name }},

Cảm ơn bạn đã đặt phòng tại khách sạn của chúng tôi. Đây là xác nhận đặt phòng của bạn:

**Mã đặt phòng:** {{ $booking->id }}

**Email khách hàng:** {{ $payment->email }}

**Ngày nhận phòng:** {{ $booking->checkin_at }}

**Ngày trả phòng:** {{ $booking->checkout_at }}

**Số lượng khách:** {{ $booking->number_guests }}

------------

Thông tin phòng chi tiết
@component('mail::table')
|     số phòng     |    kiểu phòng      |   gói ưu đãi phòng   |
| :------------- |:-------------|:-------------|
@foreach($booking['rooms'] as $item)
|{{$item['room_number']}}|{{$item['room_type']}}|{{$item['packet']}}|
@endforeach
@endcomponent

------------

Thông tin thanh toán

**email:** {{ $payment->email }}

**thành phố:** {{ $payment->city }}

**quận/huyện:** {{ $payment->state }}

**mã bưu điện:** {{ $payment->post_code }}

**ngày thanh toán:** {{ $payment->payment_date}}

**phương thức thanh toán:** {{ $payment->payment_method }}

**Tổng tiền của đơn thanh toán:** {{ number_format($payment->payment_amount, 0, ',', '.') }} VND

------------

**Tổng số tiền:** {{ number_format($booking->total_price, 0, ',', '.') }} VND

Nếu bạn cần thay đổi hoặc hủy bỏ đặt phòng, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại sau:

- Email: support@yourhotel.com
- Điện thoại: (012) 345-6789

Chúng tôi rất mong được chào đón bạn tại khách sạn của chúng tôi. Nếu bạn có bất kỳ yêu cầu đặc biệt nào hoặc cần thêm thông tin, đừng ngần ngại liên hệ với chúng tôi.

Chân thành cảm ơn,

{{ config('app.name') }}
@component('mail::subcopy')
Nếu bạn gặp vấn đề khi nhấp vào nút "Xem Chi Tiết Đặt Phòng", hãy sao chép và dán URL sau vào trình duyệt của bạn:
@endcomponent
@endcomponent
