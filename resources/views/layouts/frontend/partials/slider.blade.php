<style>
.typing-demo {
  width: 24ch;
  animation: typing 5s steps(22), blink .5s step-end infinite alternate;
  white-space: nowrap;
  overflow: hidden;
  border-right: 1px solid;
  font-family: monospace;
  font-size: 1em;
}

@keyframes typing {
  from {
    width: 0
  }
}
    
@keyframes blink {
  50% {
    border-color: transparent
  }
}
</style>

<div class="slider-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div class="slider-content">
                    <h2>
                        {{ $heroSectionData->title ?? null }}</h2>
                    <p>{{ $heroSectionData->des ?? null }}</p>
                    <div class="slider-btn">
                        <a href="{{ $heroSectionData->start_url ?? '#' }}">{{ $heroSectionData->start_text ?? null }}</a>
                        <a href="{{ $heroSectionData->contact_url ?? '#' }}">{{ $heroSectionData->contact_text ?? null }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="slider-right f-right">
                    <img class="img-fluid" src="https://lifegeegs.com/admin/1698167161124161.png" alt="">
                </div>
            </div>
        </div>
    </div>
</div>
</div>