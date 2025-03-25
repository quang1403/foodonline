import os
import sys
import qrcode

# Lấy số tiền từ tham số dòng lệnh
if len(sys.argv) > 1 and sys.argv[1].strip():
    amount = sys.argv[1].strip()
else:
    print("Thiếu tham số số tiền!")
    sys.exit(1)

# Tạo thư mục qr_codes nếu chưa có
qr_directory = "qr_codes"
if not os.path.exists(qr_directory):
    os.makedirs(qr_directory)

# Tạo nội dung mã QR
qr_data = f"Thanh toán: {amount} VND"
qr = qrcode.make(qr_data)

# Lưu QR Code thành file ảnh
qr_path = os.path.join(qr_directory, f"qr_{amount}.png")
qr.save(qr_path)

# Xuất đường dẫn ảnh để PHP sử dụng
print(qr_path)
