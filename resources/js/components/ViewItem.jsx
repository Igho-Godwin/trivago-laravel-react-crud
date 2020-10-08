import React,{Component} from 'react';
import {Pagination} from 'react-laravel-paginex';


class ViewItem extends Component {

    constructor(props) {
        super(props);
        this.getData = this.getData.bind(this);

        this.state = {
            allItems:{data:[]},
            currentItemId:''
        };
    }

    getItems(){

        const myRequest = new Request('api/items/all', {
            method: 'GET',
            mode: 'cors',
        });

        fetch(myRequest)
            .then(response => response.json())
            .then(data => {
                let items = data.items;
                this.setState({allItems:items});
            });

    }

    componentDidMount(){
        this.getItems();
    }

    getData(data){

        if(typeof data == 'undefined'){
            return [];
        }

        const myRequest = new Request('api/items/all?page=' + data.page, {
            method: 'GET',
            mode: 'cors',
        });

        fetch(myRequest)
            .then(response => response.json())
            .then(data => {
                let item = data.items;
                this.setState({allItems:item});
            });
    }

    deleteItem(id){
        var action = confirm("Press a button!");
        this.setState({currentItemId :id});
        if (!action) {
            return false;
        }
        fetch('api/item/delete/'+id, {
            method: 'DELETE',
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
        })
            .then(response => response.json())
            .then(data => {
                if(typeof data.message != 'undefined'){
                    let filteredItem = this.state.allItems.data.filter((item) => { return item.id != this.state.currentItemId } );
                    let allItems = this.state.allItems;
                    allItems.data = filteredItem;
                    this.setState({allItems:allItems});
                    document.getElementById(id).style.display = 'none';
                }
            })
            .catch((error) => {
            });

    }

    displayItems(){
        return this.state.allItems.data.map(filteredItem => (
            <tr key={filteredItem.id} id={filteredItem.id}>
                <td>{filteredItem.name}</td>
                <td>{filteredItem.rating}</td>
                <td>{filteredItem.category}</td>
                <td>{filteredItem.price}</td>
                <td>{filteredItem.availability}</td>
                <td><button onClick={() => {this.deleteItem(filteredItem.id)}}>Delete</button></td>
            </tr>
        ));
    }

    render(){
        return (
            <div>
                <div className="text-center margin-top-10" >
                    <h1>Delete An Item</h1>
                    <div >
                        <table className="width-70 margin-auto " border="1">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Rating</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Availability</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                {this.displayItems()}
                            </tbody>
                        </table>
                    </div>
                    {this.state.allItems.data.length ? (<Pagination changePage={this.getData} data={this.state.allItems} />):null }
                </div>
            </div>
    );
    }
}

export default ViewItem;



