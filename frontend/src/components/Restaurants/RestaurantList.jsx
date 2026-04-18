import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';

export default function RestaurantList() {
    const [restaurants, setRestaurants] = useState([]);
    const [loading, setLoading] = useState(true);
    const { token } = useAuth();

    useEffect(() => {
        fetchRestaurants();
    }, []);

    const fetchRestaurants = async () => {
        try {
            const response = await fetch('http://localhost:8000/api/restaurants', {
                headers: token ? { Authorization: `Bearer ${token}` } : {},
            });
            const data = await response.json();
            setRestaurants(data.data || data);
        } catch (error) {
            console.error('Failed to fetch restaurants:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div className="restaurants-container">
            <h2>Available Restaurants</h2>
            <div className="restaurants-grid">
                {restaurants.map((restaurant) => (
                    <div key={restaurant.id} className="restaurant-card">
                        <h3>{restaurant.name}</h3>
                        <p>{restaurant.description}</p>
                        <p>⭐ Rating: {restaurant.rating}</p>
                        <p>🚗 Delivery: {restaurant.delivery_time} mins</p>
                        <p>💰 Fee: ${restaurant.delivery_fee}</p>
                        <button>View Menu</button>
                    </div>
                ))}
            </div>
        </div>
    );
}
