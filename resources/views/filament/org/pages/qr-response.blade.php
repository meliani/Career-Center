<div class="flex justify-center items-center min-h-screen flex-col">
    @if(isset($studentId) && isset($internshipId))
    <div class="text-green-500 text-9xl">
        &#10004;
    </div>
    <div class="text-center mt-4">
        <p class="text-xl">Document authentique</p>
        <p class="text-xl">Student ID: {{ $studentId }}</p>
        <p class="text-xl">Internship ID: {{ $internshipId }}</p>
    </div>
    @else
    <div class="text-red-500 text-xl">
        Ce document n'est pas authentique ou à subit une altération. Veuillez contacter l'administration de l'école.
    </div>
    @endif
</div>