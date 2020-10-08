import React from 'react';
import {
    Link
} from "react-router-dom";




function MenuButtons() {
    return (
            <div className="margin-top-10">
                <div className="text-center" >
                    <Link to="/create">
                        <button className="margin-right-10">Create An Item</button>
                    </Link>
                    <Link to="/viewItems">
                        <button>View Items</button>
                    </Link>
                </div>
            </div>
    );
}

export default MenuButtons;

