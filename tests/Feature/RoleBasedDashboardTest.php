<?php

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::forget('weather:location:1:snapshot');
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/*' => Http::response([], 503),
    ]);
});

test('admins are redirected to the administrator dashboard after login', function () {
    $admin = User::factory()->create(['role' => 'Administrator']);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
        'role' => 'Administrator',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard', absolute: false));
});

test('staff are redirected to the staff dashboard after login', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $response = $this->post('/login', [
        'email' => $staff->email,
        'password' => 'password',
        'role' => 'Staff',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('dashboard'))->assertRedirect(route('staff.dashboard', absolute: false));
});

test('students are redirected to the student dashboard after login', function () {
    $student = User::factory()->create(['role' => 'Student']);

    $response = $this->post('/login', [
        'email' => $student->email,
        'password' => 'password',
        'role' => 'Student',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('dashboard'))->assertRedirect(route('student.dashboard', absolute: false));
});

test('each non-admin role is redirected to its own dashboard after login', function (string $role, string $routeName, string $greeting) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'role' => $role,
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->get(route('dashboard'))
        ->assertRedirect(route($routeName, absolute: false));
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee($greeting);
})->with([
    [User::THESIS_COORDINATOR, 'coordinator.dashboard', 'Welcome Thesis Coordinator!'],
    [User::FACULTY_ADVISER, 'adviser.dashboard', 'Welcome Faculty Adviser!'],
    [User::PANEL_MEMBER, 'panel.dashboard', 'Welcome Panel Member!'],
]);

test('administrators cannot access staff or student dashboards', function () {
    $admin = User::factory()->create(['role' => 'Administrator']);

    $this->actingAs($admin)->get(route('staff.dashboard'))->assertForbidden();
    $this->actingAs($admin)->get(route('student.dashboard'))
        ->assertForbidden();
});

test('separated role dashboards reject users with a different role', function (string $userRole, string $dashboardRoute) {
    $this->actingAs(User::factory()->create(['role' => $userRole]))
        ->get(route($dashboardRoute))
        ->assertForbidden();
})->with([
    [User::STAFF, 'coordinator.dashboard'],
    [User::THESIS_COORDINATOR, 'adviser.dashboard'],
    [User::FACULTY_ADVISER, 'panel.dashboard'],
    [User::PANEL_MEMBER, 'student.dashboard'],
    [User::STUDENT, 'coordinator.dashboard'],
]);

test('staff cannot access administrator or student dashboards', function () {
    $staff = User::factory()->create(['role' => 'Staff']);

    $this->actingAs($staff)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($staff)->get(route('student.dashboard'))->assertForbidden();
});

test('students cannot access administrator or staff dashboards', function () {
    $student = User::factory()->create(['role' => 'Student']);

    $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($student)->get(route('staff.dashboard'))->assertForbidden();
});

test('authorized dashboards display the users role greeting', function () {
    $this->actingAs(User::factory()->create(['role' => 'Administrator']))
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Welcome Administrator!')
        ->assertSee('Role: Administrator');

    $this->actingAs(User::factory()->create(['role' => 'Staff']))
        ->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSee('Welcome Staff!')
        ->assertSee('Role: Staff');

    $this->actingAs(User::factory()->create(['role' => 'Student']))
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertSee('Welcome Student!')
        ->assertSee('Role: Student');
});

test('administrator and staff dashboards display thesis records', function () {
    $thesis = Thesis::factory()->create();

    $this->actingAs(User::factory()->create(['role' => User::ADMINISTRATOR]))
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee($thesis->title);

    $this->actingAs(User::factory()->create(['role' => User::STAFF]))
        ->get(route('staff.dashboard'))
        ->assertOk()
        ->assertSee($thesis->title);
});

test('student dashboard displays only the students thesis records', function () {
    $student = User::factory()->create(['role' => User::STUDENT]);
    $ownThesis = Thesis::factory()->create(['student_id' => $student->id]);
    $otherThesis = Thesis::factory()->create();

    $this->actingAs($student)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertSee($ownThesis->title)
        ->assertDontSee($otherThesis->title);
});

test('only administrators can see staff and student workplace assignments', function () {
    $staff = User::factory()->create([
        'role' => User::STAFF,
        'workplace' => 'Engineering Building',
    ]);
    $student = User::factory()->create([
        'role' => User::STUDENT,
        'workplace' => 'North Campus',
    ]);
    $admin = User::factory()->create(['role' => User::ADMINISTRATOR]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee($staff->name)
        ->assertSee($staff->workplace)
        ->assertSee($student->name)
        ->assertSee($student->workplace);

    $this->actingAs($staff)
        ->get(route('staff.dashboard'))
        ->assertOk()
        ->assertDontSee($student->workplace);

    $this->actingAs($student)
        ->get(route('student.dashboard'))
        ->assertOk()
        ->assertDontSee($staff->workplace);
});
