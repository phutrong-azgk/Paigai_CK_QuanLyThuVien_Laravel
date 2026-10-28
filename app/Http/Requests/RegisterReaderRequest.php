<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Rule;
use Illuminate\Support\Str;

class RegisterReaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->input('code'))),
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'regex:/^[A-Z0-9]{5,16}$/', Rule::unique('nguoidung', 'tenDangNhap'), Rule::unique('hosodocgia', 'maSo')],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('nguoidung', 'thuDienTu')],
            'phone' => ['required', 'string', 'min:6', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'MSSV hoặc MSGV',
            'name' => 'họ và tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'birth_date' => 'ngày sinh',
            'password' => 'mật khẩu',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => ':attribute chỉ gồm chữ cái in hoa và chữ số, từ 5 đến 16 ký tự.',
            'code.unique' => ':attribute đã được sử dụng.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.confirmed' => 'Xác nhận mật khẩu chưa khớp.',
            'birth_date.before' => ':attribute phải trước ngày hôm nay.',
        ];
    }
}
