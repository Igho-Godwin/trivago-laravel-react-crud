import React from 'react';
import ReactDOM from 'react-dom';
import Header from './Header.jsx';
import ViewItem from "./ViewItem.jsx";
import CreateItem from "./CreateItem.jsx";
import {
    BrowserRouter,
    Switch,
    Route,
    Link
} from "react-router-dom";

function App() {

    return (
        <BrowserRouter>
            <div>
            <Header />
            <hr />
            <Switch>
                <Route path="/create">
                    <CreateItem />
                </Route>
                <Route path="/viewItems">
                    <ViewItem ref={(pageComponent) => {window.pageComponent = pageComponent}} />
                </Route>
            </Switch>
            </div>
        </BrowserRouter>
    );

}

export default App;

ReactDOM.render(<App />, document.getElementById('app'));




