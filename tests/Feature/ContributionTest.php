<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_contribute_new_book_and_admin_can_see_it(): void
    {
        // Given I am logged in as a user
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        // When I go to the Filament admin panel
        $response = $this->get(route('filament.admin.pages.dashboard'));
        $response->assertSuccessful();

        // When I try to contribute a book
        $response = $this->post(route('filament.admin.resources.books.contribute'), [
            'isbn' => '9782070423528'
        ]);

        // Then I should see success message
        $response->assertSessionHas('success', 'Livre en cours d\'ajout au catalogue');

        // And a new book record should be created
        $this->assertDatabaseHas('books', [
            'status' => 'contribution_to_qualify',
            'owner_id' => $user->id,
            'support_id' => 1,
            'isbn' => '9782070423528'
        ]);

        // Given I am logged in as a super_admin
        $admin = User::factory()->superAdmin()->create();
        $this->actingAs($admin);

        // When I go to the admin catalogue
        $response = $this->get(route('filament.admin.resources.book-admins.index'));

        // Then I should see the contributed book
        $response->assertSee('9782070423528');
    }

    public function test_user_cannot_contribute_existing_book(): void
    {
        // Given I am logged in as a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // And a book already exists
        Book::factory()->create([
            'isbn' => '9782070423528'
        ]);

        // When I try to contribute the same book
        $response = $this->post(route('books.contribute'), [
            'isbn' => '9782070423528'
        ]);

        // Then I should see an error message
        $response->assertSessionHas('error', 'Ce livre existe déjà dans notre catalogue');
    }
} 