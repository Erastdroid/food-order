import React, { useState } from 'react';
import { useAuth } from '../../context/AuthContext';

export default function ReviewForm({ orderId, restaurantId, onSuccess }) {
    const [rating, setRating] = useState(5);
    const [comment, setComment] = useState('');
    const [reviewType, setReviewType] = useState('restaurant');
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(false);
    const { token } = useAuth();

    const handleImageChange = (e) => {
        setImages(e.target.files);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('restaurant_id', restaurantId);
            formData.append('rating', rating);
            formData.append('comment', comment);
            formData.append('review_type', reviewType);

            for (let i = 0; i < images.length; i++) {
                formData.append(`images[${i}]`, images[i]);
            }

            const response = await fetch('http://localhost:8000/api/reviews', {
                method: 'POST',
                headers: { Authorization: `Bearer ${token}` },
                body: formData,
            });

            if (response.ok) {
                onSuccess();
                setRating(5);
                setComment('');
                setImages([]);
            }
        } catch (error) {
            console.error('Failed to submit review:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="review-form">
            <h3>Share Your Experience</h3>

            <div className="form-group">
                <label>Review Type</label>
                <select value={reviewType} onChange={(e) => setReviewType(e.target.value)}>
                    <option value="restaurant">Restaurant</option>
                    <option value="food">Food Quality</option>
                    <option value="delivery">Delivery Service</option>
                </select>
            </div>

            <div className="form-group">
                <label>Rating</label>
                <div className="star-rating">
                    {[1, 2, 3, 4, 5].map((star) => (
                        <span
                            key={star}
                            onClick={() => setRating(star)}
                            className={`star ${rating >= star ? 'filled' : ''}`}
                        >
                            ⭐
                        </span>
                    ))}
                </div>
            </div>

            <div className="form-group">
                <label>Comment</label>
                <textarea
                    value={comment}
                    onChange={(e) => setComment(e.target.value)}
                    placeholder="Share your experience..."
                    rows="4"
                />
            </div>

            <div className="form-group">
                <label>Add Photos (Optional)</label>
                <input type="file" multiple accept="image/*" onChange={handleImageChange} />
            </div>

            <button type="submit" disabled={loading}>
                {loading ? 'Submitting...' : 'Submit Review'}
            </button>
        </form>
    );
}
