<div id="soal-{{ $item->id }}"
     class="card shadow-none border mb-3 overflow-hidden question-item">

    <div class="card-header p-0 border-0 bg-white">

        <button class="btn btn-block text-left p-3 d-flex justify-content-between align-items-center shadow-none"
                data-toggle="collapse"
                data-target="#c{{ $item->id }}">

            <div class="d-flex align-items-center">

                <div class="badge badge-light text-primary px-3 py-2 mr-3">
                    {{ $loop->iteration }}
                </div>

                <span class="small font-weight-bold text-dark text-truncate pr-2">
                    {{ Str::limit(strip_tags($item->pertanyaan), 55) }}
                </span>

            </div>

            <i class="fas fa-chevron-down text-muted"
               style="font-size:10px"></i>

        </button>

    </div>

    <div id="c{{ $item->id }}"
         class="collapse {{ session('highlight') == $item->id ? 'show' : '' }}"
         data-parent="#accSoal">

        <div class="card-body bg-light border-top">

            <!-- ACTION -->
            <div class="d-flex justify-content-end mb-3">

                <a href="{{ route('soal.edit', $item->id) }}"
                   class="btn btn-sm btn-white shadow-sm text-primary mr-2">

                    <i class="fas fa-pen mr-1"></i>
                    Edit

                </a>

                <form action="{{ route('soal.destroy', $item->id) }}"
                      method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-sm btn-white shadow-sm text-danger"
                            onclick="return confirm('Hapus soal?')">

                        <i class="fas fa-trash"></i>

                    </button>

                </form>

            </div>

            <!-- PERTANYAAN -->
            <div class="small text-dark mb-3 math-render">
                {!! $item->pertanyaan !!}
            </div>

            @if($item->gambar_soal)

            <div class="mb-4">

                <img src="{{ asset('storage/soal/' . $item->gambar_soal) }}"
                     class="img-fluid rounded border shadow-sm"
                     style="max-height:250px; object-fit:contain;">

            </div>

            @endif

            <!-- JAWABAN -->
            <div class="list-group list-group-flush">

                @foreach($item->jawaban as $idx => $jw)

                <div class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-start">

                    <span class="answer-badge {{ $jw->jawaban_benar ? 'bg-success text-white' : 'bg-light text-dark' }} mr-3">
                        {{ chr(65 + $idx) }}
                    </span>

                    <div class="small math-render {{ $jw->jawaban_benar ? 'text-success font-weight-bold' : 'text-dark' }}">

                        {!! $jw->teks_jawaban !!}

                        @if($jw->gambar_jawaban)

                        <div class="mt-2">

                            <img src="{{ asset('storage/jawaban/' . $jw->gambar_jawaban) }}"
                                 class="img-fluid rounded border shadow-sm"
                                 style="max-height:140px; object-fit:contain;">

                        </div>

                        @endif

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

</div>