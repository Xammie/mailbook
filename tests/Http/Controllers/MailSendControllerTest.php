<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Tests\Mails\TestMail;

it('can disable', function () {
    config()->set('mailbook.enable_send', false);
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertDontSeeText('send to')
        ->assertDontSee('example@mail.com');
});
it('can enable', function () {
    config()->set('mailbook.enable_send', true);
    Mailbook::add(TestMail::class);

    get(route('mailbook.dashboard', ['class' => TestMail::class]))
        ->assertSuccessful()
        ->assertSeeText('send to')
        ->assertSee('example@mail.com');
});
it('can send', function () {
    Mailbook::add(TestMail::class);
    $mailable = Mailbook::mailables()->first()->class();
    $email = 'test@mail.com';

    $mock = Mockery::mock(Illuminate\Mail\Mailer::class);
    $mock->shouldReceive('to')
        ->once()
        ->with($email)
        ->andReturn($mock);
    $mock->shouldReceive('send')
        ->once();

    app()->instance(Illuminate\Mail\Mailer::class, $mock);

    post(route('mailbook.send', ['email' => $email, 'item' => $mailable]))
        ->assertSuccessful()
        ->assertStatus(200)
        ->assertSessionHas('success');

    $mock->mockery_verify();
    Mockery::close();
});
it('cannot send with invalid email', function () {
    Mailbook::add(TestMail::class);
    $mailable = Mailbook::mailables()->first()->class();
    $email = '::invalid-email::';

    $mock = Mockery::mock(Illuminate\Mail\Mailer::class);
    $mock->shouldNotReceive('to')
        ->andReturn($mock);
    $mock->shouldNotReceive('send');
    app()->instance(Illuminate\Mail\Mailer::class, $mock);

    post(route('mailbook.send', ['email' => $email, 'item' => $mailable]))
        ->assertInvalid()
        ->assertSessionHasErrorsIn('email');

    $mock->mockery_verify();
    Mockery::close();
});
it('cannot send without email', function () {
    Mailbook::add(TestMail::class);
    $mailable = Mailbook::mailables()->first();

    $mock = Mockery::mock(Illuminate\Mail\Mailer::class);
    $mock->shouldNotReceive('to')
        ->andReturn($mock);
    $mock->shouldNotReceive('send');
    app()->instance(Illuminate\Mail\Mailer::class, $mock);

    post(route('mailbook.send', ['item' => $mailable->class()]))
        ->assertInvalid()
        ->assertSessionHasErrorsIn('email');

    $mock->mockery_verify();
    Mockery::close();
});
it('cannot send with invalid MailableItem', function () {
    Mailbook::add(TestMail::class);

    $mock = Mockery::mock(Illuminate\Mail\Mailer::class);
    $mock->shouldNotReceive('to')
        ->andReturn($mock);
    $mock->shouldNotReceive('send');
    app()->instance(Illuminate\Mail\Mailer::class, $mock);

    post(route('mailbook.send', ['email' => 'example@mail.com', 'item' => '::invalid-mailable-item::']))
        ->assertStatus(422);

    $mock->mockery_verify();
    Mockery::close();
});
it('cannot send without MailableItem', function () {
    Mailbook::add(TestMail::class);

    $mock = Mockery::mock(Illuminate\Mail\Mailer::class);
    $mock->shouldNotReceive('to')
        ->andReturn($mock);
    $mock->shouldNotReceive('send');
    app()->instance(Illuminate\Mail\Mailer::class, $mock);

    post(route('mailbook.send', ['email' => 'example@mail.com']))
        ->assertInvalid()
        ->assertSessionHasErrorsIn('item');

    $mock->mockery_verify();
    Mockery::close();
});
