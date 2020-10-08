import React,{Component} from 'react';

class CreateItem extends Component {

    constructor(props) {
        super(props);
        this.state = {
            errors:'',
            success:''
        };
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    displayError(errors){
        var errors = Object.keys(errors).map(function(key) {
            return <div key={key}>{errors[key]}</div>
        });
        return errors;
    }

    serializeForm(form){
        var obj = {};
        var formData = new FormData(form);
        var location = {};
        var locationProperties = new Map([
                                         ["city","city"],["zip_code","zip_code"]
                                        ,["country","country"],["state","state"],
                                         ["address","address"]
                                 ]);

        for (var key of formData.keys()) {
            if(locationProperties.has(key)){
                location[key] = formData.get(key);
                continue;
            }
            obj[key] = formData.get(key);
        }
        obj['location'] = location;
        return obj;
    };

    display_error_or_success(data){

        if(typeof data.errors != 'undefined'){
            let errors = data.errors;
            this.setState({errors:errors});
            return false;
        }

        this.setState({errors:""});
        this.setState({success:'Item Created Successfully'});
        document.getElementById("createItemForm").reset();

    }

    handleSubmit(event){
        event.preventDefault();
        var data = this.serializeForm(event.target);
        fetch('api/item/create', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
             this.display_error_or_success(data);
        })
        .catch((error) => {
        });
    }

    render() {
        return (
            <div className="container">
                <div className="text-center margin-top-20">
                    <h1>Create An Item</h1>
                    <div className="margin-bottom-20">
                        <div id="errors" class="color-red margin-auto">{this.state.errors && this.displayError(this.state.errors)}</div>
                        <div id="success" className="color-blue">{this.state.success}</div>
                    </div>
                    <form id="createItemForm" onSubmit={this.handleSubmit}>
                        <div className="display-block form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">Item Name</label>
                                <input type="text" className="textfield-style width-170" name="name" maxLength="10"
                                       placeholder="item name e.g hotel" required/>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">Rating</label>
                                <input type="number" min="0" max="5" className="textfield-style width-170" name="rating"
                                       placeholder="rating e.g 5" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">Category</label>
                                <select className="textfield-style width-170" name='category' required>
                                    <option>select</option>
                                    <option>hotel</option>
                                    <option>alternative</option>
                                    <option>hostel</option>
                                    <option>lodge</option>
                                    <option>resort</option>
                                    <option>guest-house</option>
                                </select>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">Zip Code</label>
                                <input type="number" min="10000" max="90000" className="textfield-style width-170"
                                       name="zip_code" placeholder="zip_code e.g 5" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">City</label>
                                <input type="text" maxLength="255" className="textfield-style width-170" name="city"
                                       placeholder="city e.g Warri,Berlin" required/>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">State</label>
                                <input type="text" maxLength="255" className="textfield-style width-170" name="state"
                                       placeholder="state e.g Lagos" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto ">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">Country</label>
                                <input type="text" maxLength="255" className="textfield-style width-170" name="country"
                                       placeholder="Country e.g Nigeria" required/>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">Address</label>
                                <input type="text" maxLength="255" className="textfield-style width-170" name="address"
                                       placeholder="e.g 10 Downey Street" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">Image Url</label>
                                <input type="url" maxLength="255" className="textfield-style width-170" name="image"
                                       placeholder="Image Url" required/>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">Reputation</label>
                                <input type="number" min="0" max="1000" className="textfield-style width-170"
                                       name="reputation" placeholder="rating e.g 5" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className="form-float-left">
                                <label className="display-block text-align-left">Price</label>
                                <input type="number" className="textfield-style width-170" name="price"
                                       placeholder="Price" required/>
                            </div>
                            <div className="form-float-right form-margin-top">
                                <label className="display-block text-align-left">Availability</label>
                                <input type="number" className="textfield-style width-170" name="availability"
                                       placeholder="avialability e.g 5" required/>
                            </div>
                        </div>
                        <div className="form-width-30 margin-auto margin-top-20 overflow-auto">
                            <div className="">
                                <button type="reset" className="width-btn-form-100">Reset</button>
                            </div>

                        </div>
                        <div className="form-width-30 margin-auto margin-top-10 overflow-auto">
                            <div className=" form-margin-top">
                                <button type="submit" className="width-btn-form-100">Submit</button>
                            </div>
                        </div>
                    </form>
                    <div></div>
                </div>
            </div>
        );
    }
}

export default CreateItem;



