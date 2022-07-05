<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Mailable
{
    private \Illuminate\Contracts\Mail\Mailable|null $mailable = null;

    public function __construct(public string $class, public Closure $closure)
    {
    }

    public function name(): string
    {
        return Str::title(Str::snake(class_basename($this->class), ' '));
    }

    public function subject(): string
    {
        return $this->getMailable()->build()->subject;
    }

    public function render(): string
    {
        return $this->getMailable()->render();
    }

    public function getMailable(): \Illuminate\Contracts\Mail\Mailable
    {
        if ($this->mailable instanceof \Illuminate\Contracts\Mail\Mailable) {
            return $this->mailable;
        }
        
        return $this->mailable = App::call($this->closure);
    }
}
