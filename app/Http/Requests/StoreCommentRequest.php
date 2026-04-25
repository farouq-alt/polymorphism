<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // L'autorisation est gérée par les Policies
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:1000'],
            'commentable_type' => ['required', 'string', Rule::in(['App\Models\Post', 'App\Models\Video'])],
            'commentable_id' => ['required', 'integer', 'exists:' . $this->getTableName() . ',id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu du commentaire est obligatoire.',
            'content.min' => 'Le commentaire doit contenir au moins 3 caractères.',
            'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'commentable_type.required' => 'Le type d\'élément est obligatoire.',
            'commentable_type.in' => 'Le type d\'élément doit être un Post ou une Video.',
            'commentable_id.required' => 'L\'identifiant de l\'élément est obligatoire.',
            'commentable_id.exists' => 'L\'élément commenté n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, gif ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'content' => 'contenu',
            'commentable_type' => 'type',
            'commentable_id' => 'identifiant',
            'image' => 'image',
        ];
    }

    /**
     * Get the table name based on commentable_type.
     */
    protected function getTableName(): string
    {
        $type = $this->input('commentable_type');
        
        return match($type) {
            'App\Models\Post' => 'posts',
            'App\Models\Video' => 'videos',
            default => 'posts',
        };
    }
}
