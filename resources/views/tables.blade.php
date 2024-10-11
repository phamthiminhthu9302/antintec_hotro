@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg " id="review">
  <div class="container-fluid py-4">
    <div class="row">

      <form id="review-form" style="display: block;">

        @csrf
        <input type="hidden" name="id" id='request_id' value="
                {{$request->request_id}}">
        <div class="rating-container">
          <p id="close-modal" style="
        color: black;
        font-size: 30px;
        margin-left: 350px; margin-top:-15px;">×</p>
          <h2 style="
       
       margin-top:-25px;">Đánh giá của khách hàng
          </h2>
          <label for="rating">Xếp hạng (1-5):</label>
          <div class="rating-stars">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
          </div>
          <div id="error-message" style="color: red; display: none; font-size :14px; text-align:center; margin-top:-8px;"></div>
          <input type="hidden" name="rating" id="rating" required>
          <label for="comment">Bình luận:</label>
          <textarea id="comment" rows="4" placeholder="Nhập nhận xét của bạn về dịch vụ..."></textarea>
          <button id="submit-review">Gửi đánh giá</button>
        </div>
      </form>
    </div>
</main>
<style>
  .rating-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
  }

  .rating-container h2 {
    margin-bottom: 10px;
    font-size: 24px;
    color: #333;
  }

  .rating-stars {
    margin-top: -10px;
    text-align: center;
  }

  .star {
    font-size: 36px;
    color: #ccc;
    cursor: pointer;
    transition: color 0.3s ease;
  }

  .star:hover,
  .star.active {
    color: #ffcc00;
  }

  textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 16px;
    resize: none;
    outline: none;
  }

  textarea:focus {
    border-color: #00b14f;
  }
</style>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll(".star");
    const ratingInput = document.getElementById("rating");
    const errorMessage = document.getElementById("error-message");
    let selectedRating = 0;
    const reviewModal = document.getElementById('review-form');
    stars.forEach(star => {
      star.addEventListener("mouseover", () => {
        const value = parseInt(star.getAttribute("data-value"));
        if (value >= selectedRating) {
          highlightStars(value);
        }

      });

      star.addEventListener("mouseout", () => {
        highlightStars(selectedRating);
      });

      star.addEventListener("click", () => {
        selectedRating = parseInt(star.getAttribute("data-value"));
        ratingInput.value = selectedRating;
        highlightStars(selectedRating);
      });
    });

    function highlightStars(count) {
      stars.forEach(star => {
        const value = parseInt(star.getAttribute("data-value"));
        star.classList.toggle("active", value <= count);
      });
    }
    document.getElementById('review-form').addEventListener('submit', function(e) {
      e.preventDefault();
      if (selectedRating === 0) {
        errorMessage.textContent = "Vui lòng chọn xếp hạng trước khi gửi đánh giá.";
        errorMessage.style.display = 'block';
        return;
      }

      let requestId = document.getElementById('request_id').value;
      let rating = document.getElementById('rating').value;
      let comment = encodeURIComponent(document.getElementById('comment').value);
      axios.post(`/review/${requestId}`, {
          rating: rating,
          comment: comment
        })
        .then(response => {
          document.getElementById('comment').value = '';
          document.getElementById("rating").value = '';
          selectedRating = 0;
          highlightStars(selectedRating);
          errorMessage.style.display = 'none';

        })
        .catch(error => {
          console.log(error);
        });
    })
    document.getElementById('close-modal').addEventListener('click', function() {
      reviewModal.style.display = 'none';
    });
  });
</script>


@endsection