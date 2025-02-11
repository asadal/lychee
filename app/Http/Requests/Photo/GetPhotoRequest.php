<?php

namespace App\Http\Requests\Photo;

use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\Contracts\HasPhoto;
use App\Http\Requests\Contracts\RequestAttribute;
use App\Http\Requests\Traits\HasPhotoTrait;
use App\Models\Photo;
use App\Policies\PhotoPolicy;
use App\Rules\RandomIDRule;
use Illuminate\Support\Facades\Gate;

class GetPhotoRequest extends BaseApiRequest implements HasPhoto
{
	use HasPhotoTrait;

	/**
	 * {@inheritDoc}
	 */
	public function authorize(): bool
	{
		return Gate::check(PhotoPolicy::IS_VISIBLE, $this->photo);
	}

	/**
	 * {@inheritDoc}
	 */
	public function rules(): array
	{
		return [
			RequestAttribute::PHOTO_ID_ATTRIBUTE => ['required', new RandomIDRule(false)],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function processValidatedValues(array $values, array $files): void
	{
		$this->photo = Photo::with(['size_variants', 'size_variants.sym_links'])
			->findOrFail($values[RequestAttribute::PHOTO_ID_ATTRIBUTE]);
	}
}
