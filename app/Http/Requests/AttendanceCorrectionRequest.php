<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'required|string|max:255',
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'requested_break_start.*' => 'nullable|date_format:H:i',
            'requested_break_end.*' => 'nullable|date_format:H:i',
        ];


    }

    public function messages()
    {
        return [
            'reason.required' => '備考を記入してください',
            'requested_clock_in.date_format' => '時間形式で入力してください',
            'requested_clock_out.date_format' => '時間形式で入力してください',
            'requested_break_start.*.date_format' => '時間形式で入力してください',
            'requested_break_end.*.date_format' => '時間形式で入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 1番：退勤が出勤より前
            if ($this->requested_clock_in && $this->requested_clock_out) {
                if ($this->requested_clock_out <= $this->requested_clock_in) {
                    $validator->errors()->add('requested_clock_out', '出勤時間もしくは退勤時間が不適切な値です');
                }
            }

            foreach ($this->requested_break_start ?? [] as $index => $breakStart) {
                $breakEnd = $this->requested_break_end[$index] ?? null;

                // 2番：休憩開始が出勤より前 または 退勤より後
                if ($breakStart && $this->requested_clock_in && $this->requested_clock_out) {
                    if ($breakStart < $this->requested_clock_in || $breakStart > $this->requested_clock_out) {
                        $validator->errors()->add('break_error', '休憩時間が不適切な値です');
                    }
                }

                // 休憩開始と終了の前後チェック
                if ($breakStart && $breakEnd) {
                    if ($breakEnd <= $breakStart) {
                        $validator->errors()->add('break_error', '休憩時間が不適切な値です');
                    }
                }

                // 3番：休憩終了が退勤より後
                if ($breakEnd && $this->requested_clock_out) {
                    if ($breakEnd > $this->requested_clock_out) {
                        $validator->errors()->add('break_error', '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
