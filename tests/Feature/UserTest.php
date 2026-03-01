<?php

namespace Tests\Feature;

use App\Models\CaptureException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_capture_in_closed_qna_with_global_exception(): void
    {
        $user = User::factory()->create();
        
        // No exception yet
        $this->assertFalse($user->canCaptureInClosedQna());

        // Create global exception (legacy, qna_id = null)
        CaptureException::create([
            'user_id' => $user->id,
            'expires_at' => now()->addHour(),
            'qna_id' => null,
        ]);

        $this->assertTrue($user->canCaptureInClosedQna());
        $this->assertTrue($user->canCaptureInClosedQna(123)); // Should allow any QNA
    }

    public function test_user_can_capture_in_closed_qna_with_specific_qna_exception(): void
    {
        $user = User::factory()->create();
        
        $qna = \App\Models\Qna::create([
            'qna' => 1,
            'year' => 2024,
            'active' => '1',
        ]);

        // Create exception for this QNA
        CaptureException::create([
            'user_id' => $user->id,
            'expires_at' => now()->addHour(),
            'qna_id' => $qna->id,
        ]);

        $this->assertTrue($user->canCaptureInClosedQna($qna->id));
        $this->assertFalse($user->canCaptureInClosedQna($qna->id + 1));
    }

    public function test_exception_expires(): void
    {
        $user = User::factory()->create();
        
        CaptureException::create([
            'user_id' => $user->id,
            'expires_at' => now()->subMinute(), // Expired
            'qna_id' => null,
        ]);

        $this->assertFalse($user->canCaptureInClosedQna());
    }
}
