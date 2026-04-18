import React, { useState } from 'react';
import { useAuth } from '../../context/AuthContext';

export default function FavoriteButton({ restaurantId, menuItemId, type, onToggle }) {
    const [isFavorite, setIsFavorite] = useState(false);
    const [loading, setLoading] = useState(false);
    const { token } = useAuth();

    const handleToggle = async () => {
        setLoading(true);

        try {
            const response = await fetch('http://localhost:8000/api/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                    restaurant_id: restaurantId,
                    menu_item_id: menuItemId,
                    type,
                }),
            });

            const data = await response.json();
            setIsFavorite(data.isFavorite);
            onToggle?.(data.isFavorite);
        } catch (error) {
            console.error('Failed to toggle favorite:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <button
            onClick={handleToggle}
            disabled={loading}
            className={`favorite-btn ${isFavorite ? 'favorited' : ''}`}
        >
            {isFavorite ? '❤️' : '🤍'} {loading ? '...' : 'Favorite'}
        </button>
    );
}
