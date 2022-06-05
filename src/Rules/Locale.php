<?php

namespace PandaZoom\LaravelUserLocale\Rules;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use PandaZoom\LaravelCustomRule\BaseCustomRule;
use function abs;
use function config;
use function implode;
use function in_array;

class Locale extends BaseCustomRule
{
    /**
     * The minimum size of the locale.
     *
     * @var int|null
     */
    protected ?int $min = 2;

    /**
     * The maximum size of the locale.
     *
     * @var int|null
     */
    protected ?int $max = 2;

    protected bool $disableSupportLocales = false;

    /**
     * Sets the minimum size of the first name.
     *
     * @param int|null $size
     * @return $this
     */
    public function min(?int $size): static
    {
        $this->min = $size === null ?: abs($size);

        return $this;
    }

    /**
     * Sets the minimum size of the first name.
     *
     * @param int|null $size
     * @return $this
     */
    public function max(?int $size): static
    {
        $this->max = $size === null ?: abs($size);

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->messages = [];

        $rules = ['string'];

        if ($this->min) {
            $rules[] = 'min:' . $this->min;
        }

        if ($this->max) {
            $rules[] = 'max:' . $this->max;
        }

        $supportLocales = (array)config('app.locales');

        if (!$this->disableSupportLocales && !empty($supportLocales)) {
            $rules[] = 'in:' . implode(',', $supportLocales);
        }

        $validator = Validator::make(
            $this->data,
            [$attribute => [...$rules, ...$this->customRules]],
            $this->validator->customMessages,
            $this->validator->customAttributes
        )->after(function (ValidatorContract $validator) use ($attribute, $value, $supportLocales): void {
            if (!is_string($value)) {
                return;
            }

            if (!$this->disableSupportLocales && !empty($supportLocales) && !in_array($value, $supportLocales, true)) {
                $validator->errors()
                    ->add(
                        $attribute,
                        $this->getErrorMessage('validation.locale.in')
                    );
            }
        });

        if ($validator->fails()) {
            return $this->fail($validator->messages()->all());
        }

        return true;
    }

    /**
     * Get the translated password error message.
     *
     * @param string $key
     * @return string
     */
    protected function getErrorMessage(string $key): string
    {
        if (($message = $this->validator->getTranslator()->get($key)) !== $key) {
            return $message;
        }

        $messages = [
            'validation.locale.in' => 'The :attribute does not exist in :other.',
        ];

        return $messages[$key];
    }
}
