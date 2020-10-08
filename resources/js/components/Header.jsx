import React from 'react';
import Logo from './Logo.jsx';
import MenuButtons from './MenuButtons.jsx';


function Header() {
    return (
        <div className="margin-bottom-10">
           <div className="text-center margin-top-10" >
               <Logo />
               <MenuButtons />
           </div>
        </div>
    );
}

export default Header;

