<div class="pg-item p-3 mb-3 shadow-sm">

    <div class="d-flex align-items-start">

        <div class="pt-2 mr-3">

            <input type="radio"
                   name="kunci_jawaban"
                   value="{{ $value }}"
                   {{ $checked ?? '' }}
                   required>

        </div>

        <div class="mr-3">

            <div class="answer-badge bg-primary text-white">
                {{ $label }}
            </div>

        </div>

        <div class="flex-grow-1">

            <textarea name="{{ $textareaName }}"
                      id="{{ $textareaId ?? '' }}"
                      class="form-control {{ $textareaClass }}">
                {!! $slot !!}
            </textarea>

            <div class="d-flex align-items-center mt-2">

                <label class="mb-0 mr-3" style="cursor:pointer;">

                    <input type="file"
                           name="{{ $fileName }}"
                           class="hidden-input"
                           onchange="updateLabelSmall(this)">

                    <small class="text-primary font-weight-bold">
                        <i class="fas fa-camera mr-1"></i>
                        {{ $uploadText ?? 'Tambah Gambar' }}
                    </small>

                </label>

                <div class="small text-success font-weight-bold file-status"
                     style="display:none;font-size:11px;"></div>

            </div>

            @if(!empty($image))

            <div class="mt-2">

                <img src="{{ $image }}"
                     class="img-fluid rounded border shadow-sm"
                     style="max-height:120px; object-fit:contain;">

            </div>

            @endif

        </div>

    </div>

</div>