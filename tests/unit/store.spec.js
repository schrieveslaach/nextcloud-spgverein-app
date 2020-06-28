import { shallowMount } from '@vue/test-utils';
import { getters } from '../../src/store';

describe('getters', () => {
  it('members sorted by city and street', () => {
    const state = {
      members: [
        {
          "id": 1,
          "salutation": "Herr",
          "firstName": "Peter",
          "lastName": "Altmeier",
          "street": "Schnitzelstraße 3",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "2000-01-01UTC"
        },
        {
          "id": 2,
          "salutation": "Herr",
          "firstName": "Jürgen",
          "lastName": "Lippenlos",
          "street": "Lispelstraße 13",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "1990-01-01UTC",
          "admissionDate": "2002-01-01UTC"
        },
        {
          "id": 3,
          "salutation": "Frau",
          "firstName": "Anke",
          "lastName": "Engelchen",
          "street": "Schillerstraße 3",
          "zipcode": "54298",
          "city": "Aachen",
          "birth": "1992-03-08UTC",
          "admissionDate": "2000-04-03UTC"
        },
        {
          "id": 4,
          "salutation": "Herr",
          "firstName": "Max",
          "lastName": "Mustermann",
          "street": "Schillerstraße 3",
          "zipcode": "0834",
          "city": "Achen",
          "birth": "1900-01-01UTC",
          "admissionDate": "2000-01-01UTC"
        }
      ] 
    };

    const members = getters.members(state);

    const streets = members.map(member => member.street);
    expect(streets).toStrictEqual(['Schillerstraße 3', 'Schillerstraße 3', 'Lispelstraße 13', 'Schnitzelstraße 3']);
  })

  it('members sorted by null city and street', () => {
    const state = {
      members: [
        {
          "id": 1,
          "salutation": "Herr",
          "firstName": "Peter",
          "lastName": "Altmeier",
          "street": "Schnitzelstraße 3",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "2000-01-01UTC"
        },
        {
          "id": 2,
          "salutation": "Herr",
          "firstName": "Jürgen",
          "lastName": "Lippenlos",
          "street": "Lispelstraße 13",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "1990-01-01UTC",
          "admissionDate": "2002-01-01UTC"
        },
        {
          "id": 3,
          "salutation": "Frau",
          "firstName": "Anke",
          "lastName": "Engelchen",
          "street": "Schillerstraße 3",
          "zipcode": "54298",
          "city": "Aachen",
          "birth": "1992-03-08UTC",
          "admissionDate": "2000-04-03UTC"
        },
        {
          "id": 4,
          "salutation": "Herr",
          "firstName": "Max",
          "lastName": "Mustermann",
          "street": "Schillerstraße 3",
          "zipcode": "0834",
          "birth": "1900-01-01UTC",
          "admissionDate": "2000-01-01UTC"
        }
      ] 
    };

    const members = getters.members(state);

    const streets = members.map(member => member.street);
    expect(streets).toStrictEqual(['Schillerstraße 3', 'Lispelstraße 13', 'Schnitzelstraße 3', 'Schillerstraße 3']);
  })

  it('members sorted by city and nullable street', () => {
    const state = {
      members: [
        {
          "id": 1,
          "salutation": "Herr",
          "firstName": "Peter",
          "lastName": "Altmeier",
          "street": "Schnitzelstraße 3",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "2000-01-01UTC"
        },
        {
          "id": 2,
          "salutation": "Herr",
          "firstName": "Jürgen",
          "lastName": "Lippenlos",
          "street": "Lispelstraße 13",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "1990-01-01UTC",
          "admissionDate": "2002-01-01UTC"
        },
        {
          "id": 3,
          "salutation": "Frau",
          "firstName": "Anke",
          "lastName": "Engelchen",
          "street": "Schillerstraße 3",
          "zipcode": "54298",
          "city": "Aachen",
          "birth": "1992-03-08UTC",
          "admissionDate": "2000-04-03UTC"
        },
        {
          "id": 4,
          "salutation": "Herr",
          "firstName": "Max",
          "lastName": "Mustermann",
          "zipcode": "0834",
          "city": "Aachen",
          "birth": "1900-01-01UTC",
          "admissionDate": "2000-01-01UTC"
        }
      ] 
    };


    const members = getters.members(state);

    const streets = members.map(member => member.street);
    expect(streets).toStrictEqual(['Schillerstraße 3', undefined, 'Lispelstraße 13', 'Schnitzelstraße 3']);
  })

  it('members sorted by city and street without street number', () => {
    const state = {
      members: [
        {
          "id": 1,
          "salutation": "Herr",
          "firstName": "Peter",
          "lastName": "Altmeier",
          "street": "Schnitzelstraße 3",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "2000-01-01UTC"
        },
        {
          "id": 2,
          "salutation": "Herr",
          "firstName": "Jürgen",
          "lastName": "Lippenlos",
          "street": "Lispelstraße 13",
          "zipcode": "0815",
          "city": "Berlin",
          "birth": "1990-01-01UTC",
          "admissionDate": "2002-01-01UTC"
        },
        {
          "id": 3,
          "salutation": "Frau",
          "firstName": "Anke",
          "lastName": "Engelchen",
          "street": "Schillerstraße",
          "zipcode": "54298",
          "city": "Aachen",
          "birth": "1992-03-08UTC",
          "admissionDate": "2000-04-03UTC"
        },
        {
          "id": 4,
          "salutation": "Herr",
          "firstName": "Max",
          "lastName": "Mustermann",
          "street": "Schillerstraße 3",
          "zipcode": "0834",
          "city": "Aachen",
          "birth": "1900-01-01UTC",
          "admissionDate": "2000-01-01UTC"
        }
      ] 
    };

    const members = getters.members(state);

    const streets = members.map(member => member.street);
    expect(streets).toStrictEqual(['Schillerstraße 3', 'Schillerstraße', 'Lispelstraße 13', 'Schnitzelstraße 3']);
  })
})
