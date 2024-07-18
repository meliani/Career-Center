<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card class="p-0">
            @push('styles')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.0.5/wordcloud2.min.js"></script>
            <style>
                #word-cloud {
                    width: 600px;
                    height: 400px;
                    border: 1px solid #ccc;
                    margin: 0 auto;
                    margin-top: 50px;
                }
            </style>
            @endpush
            <div id="word-cloud"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                const words = [
                    ['JavaScript', 30],
                    ['HTML', 20],
                    ['CSS', 25],
                    ['React', 15],
                    ['Node.js', 10],
                    ['D3.js', 5],
                    ['WordCloud.js', 35]
                ];

                WordCloud(document.getElementById('word-cloud'), {
                    list: words,
                    gridSize: Math.round(16 * document.getElementById('word-cloud').offsetWidth / 1024),
                    weightFactor: function (size) {
                        return Math.pow(size, 2.3) * document.getElementById('word-cloud').offsetWidth / 1024;
                    },
                    fontFamily: 'Times, serif',
                    color: function (word, weight) {
                        return (weight === 30) ? '#f02222' : '#c09292';
                    },
                    rotateRatio: 0,
                    rotationSteps: 0,
                    backgroundColor: '#ffe0e0'
                });
            });
            </script>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>
