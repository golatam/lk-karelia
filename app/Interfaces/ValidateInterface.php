<?php

namespace App\Interfaces;

interface ValidateInterface
{
    /**
     * @param string $status
     * @param array $rules
     * @return array
     */
    public function rules($status = '', array $rules = []): array;

    /**
     * @return array
     */
    public function messages(): array;

    /**
     * @return array
     */
    public function attributes(): array;
}
